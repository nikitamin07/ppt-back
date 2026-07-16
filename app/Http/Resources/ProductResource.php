<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Формат зеркалит ppt-front/src/entities/product/model/types.ts.
 * Цены в БД хранятся в копейках, наружу отдаются в рублях.
 */
final class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => (string) $this->description,
            'price' => $this->price / 100,
            'discount_price' => $this->discount_price !== null ? $this->discount_price / 100 : null,
            'price_unit' => $this->price_unit,
            'is_featured' => $this->is_featured,
            'is_volume_price' => $this->is_volume_price,
            'volume_price' => $this->is_volume_price ? [
                'low' => [
                    'price' => $this->volume_price_low / 100,
                    'label' => $this->volume_price_low_label,
                ],
                'medium' => [
                    'price' => $this->volume_price_medium / 100,
                    'label' => $this->volume_price_medium_label,
                ],
                'high' => $this->volume_price_high !== null ? [
                    'price' => $this->volume_price_high / 100,
                    'label' => $this->volume_price_high_label,
                ] : null,
            ] : null,
            // Загрузки админки лежат на диске public, наружу отдаются через /storage
            'image_url' => $this->image !== null ? '/storage/'.ltrim($this->image, '/') : null,
            'attributes' => $this->whenLoaded(
                'attributes',
                fn () => $this->attributes->map(fn ($attribute) => [
                    'attribute_id' => $attribute->id,
                    'name' => $attribute->name,
                    'value' => $attribute->pivot->value,
                ]),
                [],
            ),
            'related_product_ids' => $this->whenLoaded(
                'related',
                fn () => $this->related->pluck('id'),
                [],
            ),
        ];
    }
}
