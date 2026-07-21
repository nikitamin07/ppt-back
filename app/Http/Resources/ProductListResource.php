<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\Concerns\ResolvesUploadUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Товар в списках (/products, /products/featured, POST /products/filter) — поля карточки.
 * За description и характеристиками идти в /products/{categorySlug}/{productSlug}: ProductResource
 * наследует этот набор и дополняет его.
 *
 * Формат зеркалит ppt-front/src/entities/product/model/types.ts. Цены в БД в копейках, наружу — в рублях.
 */
class ProductListResource extends JsonResource
{
    use ResolvesUploadUrl;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            // Слаг прямой категории — корневой или подкатегории
            'category_slug' => $this->whenLoaded('category', fn () => $this->category?->slug, null),
            // Путь категории: фронт строит адрес /catalog/{category_path}/{slug}
            'category_path' => $this->whenLoaded('category', fn () => $this->category?->path(), null),
            'slug' => $this->slug,
            'name' => $this->name,
            'price' => $this->price / 100,
            'discount_price' => $this->discount_price !== null ? $this->discount_price / 100 : null,
            'price_unit' => $this->price_unit,
            'is_featured' => $this->is_featured,
            'is_volume_price' => $this->is_volume_price,
            'volume_price' => $this->is_volume_price ? [
                'low' => self::tier($this->volume_price_low, $this->volume_price_low_label),
                'medium' => self::tier($this->volume_price_medium, $this->volume_price_medium_label),
                'high' => self::tier($this->volume_price_high, $this->volume_price_high_label),
            ] : null,
            // Карточке хватает первой картинки; вся галерея — только в ProductResource
            'image_url' => self::uploadUrls($this->images)[0] ?? null,
            'manufacturer' => $this->whenLoaded(
                'manufacturer',
                fn () => $this->manufacturer !== null ? [
                    'id' => $this->manufacturer->id,
                    'name' => $this->manufacturer->name,
                ] : null,
                null,
            ),
        ];
    }

    /** Ярус объёмной цены; null только у high — low и medium гарантирует CHECK в БД. */
    private static function tier(?int $price, ?string $label): ?array
    {
        return $price !== null ? ['price' => $price / 100, 'label' => $label] : null;
    }
}
