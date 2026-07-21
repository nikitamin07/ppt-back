<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Калькулятор объёма настраивается на корневой категории; подкатегории наследуют её флаг.
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->boolean('show_calculator')->default(true);
        });

        // Сухие смеси продаются мешками и штуками — считать в кубах там нечего
        DB::table('categories')->where('slug', 'dry-building-mixes')->update(['show_calculator' => false]);
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->dropColumn('show_calculator');
        });
    }
};
