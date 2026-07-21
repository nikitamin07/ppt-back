<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Категория без description: дерево каталога и списки подкатегорий.
 * Описание отдаёт только CategoryResource на запрос конкретной категории.
 */
final class CategoryListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'slug' => $this->slug,
            'name' => $this->name,
            'children' => self::collection($this->whenLoaded('children')),
        ];
    }
}
