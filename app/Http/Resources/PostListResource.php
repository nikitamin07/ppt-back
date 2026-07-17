<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\Concerns\ResolvesUploadUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Статья в списках (/posts, /posts/latest) — без тела статьи.
 * За content идти в /posts/{slug}: PostResource наследует этот набор и добавляет его.
 */
class PostListResource extends JsonResource
{
    use ResolvesUploadUrl;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'excerpt' => (string) $this->excerpt,
            'cover_image_url' => self::uploadUrl($this->cover_image),
            'published_at' => $this->published_at?->toIso8601String(),
            'tag_ids' => $this->whenLoaded('tags', fn () => $this->tags->pluck('id'), []),
        ];
    }
}
