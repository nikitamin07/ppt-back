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
            // Показывать ли калькулятор объёма (см. Product::isCalculative)
            'isCalculative' => $this->resource->isCalculative(),
            // Кубов в одной уп./шт.; null, когда цена и так за куб
            'cubes_per_pack' => $this->cubes_per_pack,
            // Вся галерея в порядке из админки; image_url — её первый элемент
            'image_urls' => self::uploadUrls($this->images),
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
