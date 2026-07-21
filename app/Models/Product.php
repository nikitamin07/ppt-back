<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\ValidationException;

final class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'manufacturer_id', 'name', 'slug', 'description', 'meta_description',
        'price', 'discount_price', 'price_unit',
        'is_volume_price',
        'volume_price_low', 'volume_price_medium', 'volume_price_high',
        'volume_price_low_label', 'volume_price_medium_label', 'volume_price_high_label',
        'images', 'is_active', 'is_featured', 'featured_position', 'cubes_per_pack',
    ];

    /** Максимум товаров в блоке «Популярные» (одна страница, без пагинации). */
    public const FEATURED_LIMIT = 8;

    /** Допустимые единицы измерения цены; дублируется CHECK-констрейнтом products_price_unit_check. */
    public const PRICE_UNITS = ['куб' => 'куб', 'уп.' => 'уп.', 'шт.' => 'шт.'];

    protected function casts(): array
    {
        return [
            'is_volume_price' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'cubes_per_pack' => 'float',
            'images' => 'array',
        ];
    }

    protected static function booted(): void
    {
        // Лимит популярных: не даём включить флаг девятому товару (форма админки
        // блокирует переключатель, это страховка для остальных путей записи).
        self::saving(function (self $product): void {
            if (
                $product->is_featured
                && $product->isDirty('is_featured')
                && static::where('is_featured', true)->whereKeyNot($product->getKey())->count() >= self::FEATURED_LIMIT
            ) {
                throw ValidationException::withMessages([
                    'is_featured' => 'Максимум популярных товаров: '.self::FEATURED_LIMIT,
                ]);
            }
        });

        // Позиция в блоке «Популярные»: новый товар встаёт в конец, снятый — теряет позицию.
        self::saving(function (self $product): void {
            if (! $product->is_featured) {
                $product->featured_position = null;
            } elseif ($product->featured_position === null) {
                $product->featured_position = (int) static::max('featured_position') + 1;
            }
        });

        // Кубы в упаковке нужны, только когда товар продаётся не кубами.
        self::saving(function (self $product): void {
            if ($product->price_unit === 'куб') {
                $product->cubes_per_pack = null;
            }
        });

        // Режимы цены взаимоисключающие: либо обычная цена (+скидка), либо объёмные тарифы.
        self::saving(function (self $product): void {
            if ($product->is_volume_price) {
                // Карточка товара на фронте всегда показывает price — держим её равной самому дешёвому
                // из заполненных тарифов (high опционален, см. products_price_mode_check).
                $product->price = $product->volume_price_high
                    ?? $product->volume_price_medium
                    ?? $product->volume_price_low
                    ?? $product->price;
                $product->discount_price = null;
            } else {
                $product->volume_price_low = null;
                $product->volume_price_medium = null;
                $product->volume_price_high = null;
                $product->volume_price_low_label = null;
                $product->volume_price_medium_label = null;
                $product->volume_price_high_label = null;
            }
        });
    }

    /**
     * Показывать ли калькулятор объёма: калькулятор включён у корневой категории
     * И объём вообще есть чем посчитать — либо цена уже за куб, либо задан cubes_per_pack.
     */
    public function isCalculative(): bool
    {
        if (! ($this->category?->showsCalculator() ?? false)) {
            return false;
        }

        return $this->price_unit === 'куб' || $this->cubes_per_pack !== null;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class)
            ->withPivot(['value', 'position'])
            ->orderByPivot('position');
    }

    public function related(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'product_related', 'product_id', 'related_product_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Блок «Популярные» в порядке, заданном админом перетаскиванием. */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true)->orderBy('featured_position');
    }

    /** Поиск по названию: подстрока без учёта регистра, спецсимволы LIKE экранируются. */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('name', 'ilike', '%'.addcslashes($term, '%_\\').'%');
    }
}
