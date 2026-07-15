<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Формат зеркалит ppt-front/src/entities/post/model/types.ts. */
final class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'excerpt' => (string) $this->excerpt,
            'content' => (string) $this->content,
            // Загрузки админки лежат на диске public, наружу отдаются через /storage
            'cover_image_url' => $this->cover_image !== null ? '/storage/'.ltrim($this->cover_image, '/') : null,
            'published_at' => $this->published_at?->toIso8601String(),
            'tag_ids' => $this->whenLoaded('tags', fn () => $this->tags->pluck('id'), []),
        ];
    }
}
