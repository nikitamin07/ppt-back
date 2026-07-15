<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ProductController extends Controller
{
    private const PER_PAGE = 24;

    public function index(Request $request): AnonymousResourceCollection
    {
        $products = $this->filtered($request)
            ->with(['attributes', 'related'])
            ->orderBy('id')
            // Без ?page отдаём весь список (каталог небольшой), с ?page — постранично.
            ->when(
                $request->filled('page'),
                fn (Builder $query) => $query->forPage(max(1, (int) $request->query('page')), self::PER_PAGE),
            )
            ->get();

        return ProductResource::collection($products);
    }

    public function count(Request $request): JsonResponse
    {
        return response()->json(['count' => $this->filtered($request)->count()]);
    }

    public function show(string $categorySlug, string $productSlug): ProductResource
    {
        $product = Product::active()
            ->where('slug', $productSlug)
            ->whereRelation('category', 'slug', $categorySlug)
            ->with(['attributes', 'related'])
            ->firstOrFail();

        return new ProductResource($product);
    }

    /** Общие фильтры списка и счётчика: ?category=slug (корень или подкатегория), ?query=поиск. */
    private function filtered(Request $request): Builder
    {
        return Product::active()
            ->when($request->query('category'), function (Builder $query, string $slug): void {
                // Слаг корневой категории включает товары её подкатегорий.
                $query->whereIn('category_id', Category::query()
                    ->where('slug', $slug)
                    ->orWhereRelation('parent', 'slug', $slug)
                    ->pluck('id'));
            })
            ->when(
                $request->query('query'),
                fn (Builder $query, string $term) => $query->where('name', 'ilike', '%'.addcslashes($term, '%_\\').'%'),
            );
    }
}
