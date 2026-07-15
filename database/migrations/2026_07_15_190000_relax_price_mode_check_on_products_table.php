<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Смягчаем правило объёмного режима: достаточно ДВУХ заполненных тарифов из трёх.
    public function up(): void
    {
        DB::statement('ALTER TABLE products DROP CONSTRAINT products_price_mode_check');
        DB::statement(<<<'SQL'
            ALTER TABLE products ADD CONSTRAINT products_price_mode_check CHECK (
                (
                    is_volume_price
                    AND discount_price IS NULL
                    AND (volume_price_before_10 IS NOT NULL)::int
                        + (volume_price_10_to_20 IS NOT NULL)::int
                        + (volume_price_after_20 IS NOT NULL)::int >= 2
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
};
