<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description',
        'price', 'discount_price', 'price_unit',
        'is_volume_price',
        'volume_price_low', 'volume_price_medium', 'volume_price_high',
        'volume_price_low_label', 'volume_price_medium_label', 'volume_price_high_label',
        'image', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_volume_price' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Режимы цены взаимоисключающие: либо обычная цена (+скидка), либо объёмные тарифы.
        static::saving(function (self $product): void {
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
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
}
