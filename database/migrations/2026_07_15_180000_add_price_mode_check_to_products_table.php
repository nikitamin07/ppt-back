<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Режимы цены взаимоисключающие (см. Product::booted()):
    // объёмный  -> discount_price NULL, все три тарифа заполнены;
    // обычный   -> все три тарифа NULL.
    public function up(): void
    {
        // Нормализуем старые данные (у части товаров остались мусорные тарифы при выключенном флаге)
        DB::statement('UPDATE products SET volume_price_before_10 = NULL, volume_price_10_to_20 = NULL, volume_price_after_20 = NULL WHERE NOT is_volume_price');
        DB::statement('UPDATE products SET discount_price = NULL WHERE is_volume_price');

        DB::statement(<<<'SQL'
            ALTER TABLE products ADD CONSTRAINT products_price_mode_check CHECK (
                (
                    is_volume_price
                    AND discount_price IS NULL
                    AND volume_price_before_10 IS NOT NULL
                    AND volume_price_10_to_20 IS NOT NULL
                    AND volume_price_after_20 IS NOT NULL
                ) OR (
                    NOT is_volume_price
                    AND volume_price_before_10 IS NULL
                    AND volume_price_10_to_20 IS NULL
                    AND volume_price_after_20 IS NULL
                )
            )
        SQL);
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE products DROP CONSTRAINT products_price_mode_check');
    }
};
