<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;

/** Статья целиком (/posts/{slug}): поля списка + content (сырой HTML из админки). */
final class PostResource extends PostListResource
{
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'content' => (string) $this->content,
        ];
    }
}
