<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Единица измерения приводится к трём допустимым значениям.
     * До этого у 38 товаров там лежали «руб» и «коп» — мусор, затёкший при переносе со старого сайта.
     */
    private const ALLOWED = ['куб', 'уп.', 'шт.'];

    /** Фасованное мешком/рулоном — упаковка. */
    private const PACKS = [49, 50, 54, 57, 58, 59, 60, 61, 62, 84];

    public function up(): void
    {
        // Всё, что не утеплитель в кубах, по умолчанию штучное
        DB::table('products')->whereNotIn('price_unit', self::ALLOWED)->update(['price_unit' => 'шт.']);
        DB::table('products')->whereIn('id', self::PACKS)->update(['price_unit' => 'уп.']);

        DB::statement("ALTER TABLE products ADD CONSTRAINT products_price_unit_check CHECK (price_unit IN ('куб', 'уп.', 'шт.'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE products DROP CONSTRAINT products_price_unit_check');
    }
};
