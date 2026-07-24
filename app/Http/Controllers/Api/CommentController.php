<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

final class CommentController extends Controller
{
    /** Тот же текст к тому же товару в этом окне молча игнорируется: двойной клик и простой спам. */
    private const DUPLICATE_WINDOW_MINUTES = 10;

    /** Форма отзыва на карточке товара. Запись создаётся неопубликованной и ждёт модерации. */
    public function store(Request $request): Response
    {
        $data = $request->validate([
            // Только активный товар: на скрытый отзыв всё равно негде было бы посмотреть
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')->where('is_active', true)],
            'author' => ['required', 'string', 'min:2', 'max:80'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'body' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $isDuplicate = Comment::where('product_id', $data['product_id'])
            ->where('body', $data['body'])
            ->where('created_at', '>=', now()->subMinutes(self::DUPLICATE_WINDOW_MINUTES))
            ->exists();

        if (! $isDuplicate) {
            // is_approved не принимаем из тела: остаётся false до одобрения в админке
            Comment::create($data);
        }

        return response()->noContent();
    }
}
