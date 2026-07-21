<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Сколько кубов в одной упаковке/штуке: позволяет считать калькулятором товар,
    // который продаётся не кубами. Для price_unit = «куб» всегда NULL.
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->decimal('cubes_per_pack', 8, 4)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn('cubes_per_pack');
        });
    }
};
