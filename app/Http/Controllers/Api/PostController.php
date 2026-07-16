<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class PostController extends Controller
{
    private const PER_PAGE = 8;

    /** Блок последних статей на главной. */
    private const LATEST_LIMIT = 6;

    public function index(Request $request): AnonymousResourceCollection
    {
        $posts = Post::published()
            ->with('tags')
            ->latest('published_at')
            ->when(
                $request->query('tag'),
                fn (Builder $query, string $slug) => $query->whereRelation('tags', 'slug', $slug),
            )
            ->when(
                $request->query('query'),
                fn (Builder $query, string $term) => $query->where('title', 'ilike', '%'.addcslashes($term, '%_\\').'%'),
            )
            ->when(
                $request->filled('page'),
                fn (Builder $query) => $query->forPage(max(1, (int) $request->query('page')), self::PER_PAGE),
            )
            ->get();

        return PostResource::collection($posts);
    }

    /** Главная страница: до 6 последних опубликованных статей (меньше — сколько есть). */
    public function latest(): AnonymousResourceCollection
    {
        return PostResource::collection(
            Post::published()
                ->with('tags')
                ->latest('published_at')
                ->take(self::LATEST_LIMIT)
                ->get(),
        );
    }

    public function show(string $slug): PostResource
    {
        return new PostResource(
            Post::published()->with('tags')->where('slug', $slug)->firstOrFail(),
        );
    }
}
