<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            // Дерево строго в один уровень: категория -> подкатегория (валидируется в приложении)
            $table->foreignId('parent_id')->nullable()->constrained('categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Справочник характеристик ("плотность", "влагостойкость", ...)
        Schema::create('attributes', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            // Цены — integer в минорных единицах (копейках), как в legacy
            $table->unsignedInteger('price')->default(0);
            $table->unsignedInteger('discount_price')->nullable();
            $table->string('price_unit')->default('куб');
            $table->boolean('is_volume_price')->default(false);
            $table->unsignedInteger('volume_price_before_10')->nullable();
            $table->unsignedInteger('volume_price_10_to_20')->nullable();
            $table->unsignedInteger('volume_price_after_20')->nullable();
            $table->string('template')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Значение характеристики у конкретного товара
        Schema::create('attribute_product', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained()->cascadeOnDelete();
            $table->string('value');
            $table->unsignedSmallInteger('position')->default(0);
            $table->unique(['product_id', 'attribute_id']);
        });

        Schema::create('product_related', function (Blueprint $table): void {
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('related_product_id')->constrained('products')->cascadeOnDelete();
            $table->primary(['product_id', 'related_product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_related');
        Schema::dropIfExists('attribute_product');
        Schema::dropIfExists('products');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('categories');
    }
};
