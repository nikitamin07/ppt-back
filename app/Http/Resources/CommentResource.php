<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Отзыв в карточке товара. Отдаются только прошедшие модерацию — фильтрует запрос контроллера. */
final class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'author' => $this->author,
            'rating' => $this->rating,
            'body' => $this->body,
            // Только дата: время публикации отзыва на витрине не нужно
            'created_at' => $this->created_at->toDateString(),
        ];
    }
}
