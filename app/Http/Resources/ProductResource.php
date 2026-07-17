<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;

/** Полная карточка товара (/products/{categorySlug}/{productSlug}): поля списка + тяжёлые. */
final class ProductResource extends ProductListResource
{
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'description' => (string) $this->description,
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
