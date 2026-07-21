<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Порядок корневых категорий: задаётся перетаскиванием строк в админке.
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->unsignedSmallInteger('position')->default(0);
        });

        // Стартовый порядок — текущий (по id); Filament перенумерует при первом перетаскивании
        DB::statement('UPDATE categories SET position = id');
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->dropColumn('position');
        });
    }
};
