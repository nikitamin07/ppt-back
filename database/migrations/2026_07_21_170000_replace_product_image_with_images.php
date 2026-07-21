<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Одна картинка -> галерея. Порядок в массиве = порядок показа (перетаскивание в админке).
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->json('images')->nullable();
        });

        // Существующая одиночная картинка становится первой в галерее
        DB::table('products')
            ->whereNotNull('image')
            ->where('image', '<>', '')
            ->update(['images' => DB::raw("json_build_array(image)::json")]);

        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn('image');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->string('image')->nullable();
        });

        DB::statement("UPDATE products SET image = images->>0 WHERE json_array_length(images) > 0");

        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn('images');
        });
    }
};
