<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Одна конкретная категория — единственное место, где отдаётся description.
 * Дерево каталога использует CategoryListResource (без описания).
 */
final class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => (string) $this->description,
            'children' => CategoryListResource::collection($this->whenLoaded('children')),
        ];
    }
}
