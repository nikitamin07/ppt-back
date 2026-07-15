<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Объёмные тарифы: low/medium/high вместо жёстких порогов before_10/10_to_20/after_20,
    // плюс свободные текстовые пояснения к каждому («до 10 кубов», «до 5 паллет», ...).
    // Правило: в объёмном режиме low и medium обязательны, high — опционален.
    public function up(): void
    {
        DB::statement('ALTER TABLE products DROP CONSTRAINT products_price_mode_check');

        Schema::table('products', function (Blueprint $table): void {
            $table->renameColumn('volume_price_before_10', 'volume_price_low');
            $table->renameColumn('volume_price_10_to_20', 'volume_price_medium');
            $table->renameColumn('volume_price_after_20', 'volume_price_high');
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->string('volume_price_low_label')->nullable();
            $table->string('volume_price_medium_label')->nullable();
            $table->string('volume_price_high_label')->nullable();
        });

        DB::statement(<<<'SQL'
            ALTER TABLE products ADD CONSTRAINT products_price_mode_check CHECK (
                (
                    is_volume_price
                    AND discount_price IS NULL
                    AND volume_price_low IS NOT NULL
                    AND volume_price_medium IS NOT NULL
                ) OR (
                    NOT is_volume_price
                    AND volume_price_low IS NULL
                    AND volume_price_medium IS NULL
                    AND volume_price_high IS NULL
                )
            )
        SQL);
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE products DROP CONSTRAINT products_price_mode_check');

        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn(['volume_price_low_label', 'volume_price_medium_label', 'volume_price_high_label']);
            $table->renameColumn('volume_price_low', 'volume_price_before_10');
            $table->renameColumn('volume_price_medium', 'volume_price_10_to_20');
            $table->renameColumn('volume_price_high', 'volume_price_after_20');
        });

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
};
