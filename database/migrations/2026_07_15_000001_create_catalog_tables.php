<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            // Порядок корневых категорий: перетаскивание в админке, им же сортирует GET /api/categories
            $table->unsignedSmallInteger('position')->default(0);
            $table->string('meta_description')->nullable();
            // «Выводить калькулятор» — настройка корневой категории, подкатегории наследуют
            $table->boolean('show_calculator')->default(true);
        });

        Schema::create('manufacturers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable(); // путь на диске public (manufacturers/...)
            $table->timestamps();
        });

        // Справочник характеристик ("плотность", "влагостойкость", ...)
        Schema::create('attributes', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 80);
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Порядок колонок повторяет живую БД, чтобы migrate:fresh давал схему,
        // идентичную дампу pptbel_public.sql.
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
            // Объёмные тарифы: low/medium обязательны, high опционален; пояснения — свободный текст
            $table->boolean('is_volume_price')->default(false);
            $table->unsignedInteger('volume_price_low')->nullable();
            $table->unsignedInteger('volume_price_medium')->nullable();
            $table->unsignedInteger('volume_price_high')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->string('volume_price_low_label')->nullable();
            $table->string('volume_price_medium_label')->nullable();
            $table->string('volume_price_high_label')->nullable();
            $table->foreignId('manufacturer_id')->nullable()->constrained()->nullOnDelete();
            // «Показывать в популярных» — блок на главной, максимум 8 товаров (Product::booted())
            $table->boolean('is_featured')->default(false);
            $table->string('meta_description')->nullable();
            // Кубов в одной уп./шт. — коэффициент для калькулятора объёма
            $table->decimal('cubes_per_pack', 8, 4)->nullable();
            // Галерея: пути на диске public в порядке из админки, image_url — первый элемент
            $table->json('images')->nullable();
            // Порядок товара в блоке «Популярные»; null у непопулярных (Product::booted())
            $table->unsignedSmallInteger('featured_position')->nullable();
        });

        // Режимы цены взаимоисключающие, дублирует Product::booted() на уровне БД
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

        // Единица измерения цены — только эти три, дублирует Select в админке
        DB::statement("ALTER TABLE products ADD CONSTRAINT products_price_unit_check CHECK (price_unit IN ('куб', 'уп.', 'шт.'))");

        // Значение характеристики у конкретного товара
        Schema::create('attribute_product', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained()->cascadeOnDelete();
            $table->string('value', 20);
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
        Schema::dropIfExists('manufacturers');
        Schema::dropIfExists('categories');
    }
};
