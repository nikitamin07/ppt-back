<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
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

    /** Толщина по умолчанию, когда у товара её нет, а калькулятору нужно с чего-то начать. */
    public const DEFAULT_THICKNESS = 30;

    /** Характеристика, из которой калькулятор берёт толщину. */
    public const THICKNESS_ATTRIBUTE = 'Толщина';

    /**
     * Толщина в мм для предзаполнения калькулятора: из характеристики «Толщина»,
     * иначе дефолт. null там, где калькулятора нет вовсе — считать нечего.
     */
    public function thicknessForCalculator(): ?int
    {
        if (! ($this->category?->showsCalculator() ?? false)) {
            return null;
        }

        // getRelationValue, а не $this->attributes: внутри модели это поле Eloquent, а не связь
        $value = $this->getRelationValue('attributes')?->firstWhere('name', self::THICKNESS_ATTRIBUTE)?->pivot->value;

        return $value !== null && preg_match('/\d+/', $value, $m) ? (int) $m[0] : self::DEFAULT_THICKNESS;
    }

    /**
     * Копия товара под другую толщину: число уходит в название, слаг и характеристику
     * «Толщина» — из неё же калькулятор берёт свою толщину, отдельного поля нет.
     */
    public function duplicateWithThickness(int $mm): self
    {
        $copy = $this->replicate();
        $copy->name = self::nameWithThickness($this->name, $mm);
        $copy->slug = self::uniqueSlug($copy->name);
        $copy->is_featured = false;
        $copy->save();

        $value = $mm.' мм';
        $thicknessId = Attribute::firstOrCreate(['name' => self::THICKNESS_ATTRIBUTE])->getKey();

        // Заготовка на случай, когда у исходника «Толщины» нет: встанет в конец списка
        $pivot = [$thicknessId => [
            'value' => $value,
            'position' => (int) $this->attributes()->max('attribute_product.position') + 1,
        ]];

        foreach ($this->attributes()->get() as $attribute) {
            $pivot[$attribute->getKey()] = [
                'value' => $attribute->getKey() === $thicknessId ? $value : $attribute->pivot->value,
                'position' => $attribute->pivot->position,
            ];
        }

        $copy->attributes()->attach($pivot);

        return $copy;
    }

    /** «Плита ППТ 30 мм, 13 шт» → «Плита ППТ 50 мм, 13 шт»; нет толщины в названии — дописываем в конец. */
    private static function nameWithThickness(string $name, int $mm): string
    {
        $replaced = preg_replace('/\d+\s*мм/u', $mm.' мм', $name, 1, $count);

        return $count ? $replaced : $name.' '.$mm.' мм';
    }

    /** Слаг уникален в БД: товар такой толщины мог уже существовать — добавляем номер. */
    private static function uniqueSlug(string $name): string
    {
        $slug = $base = Str::slug($name);

        for ($i = 2; self::where('slug', $slug)->exists(); $i++) {
            $slug = $base.'-'.$i;
        }

        return $slug;
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

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
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
