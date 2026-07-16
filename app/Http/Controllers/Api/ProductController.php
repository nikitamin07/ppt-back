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
    private const PER_PAGE = 8;

    public function index(Request $request): AnonymousResourceCollection
    {
        $products = $this->filtered($request)
            ->with(['attributes', 'related'])
            ->orderBy('id')
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

    /**
     * Фильтрация каталога (страница каталога с фильтрами). JSON-тело, все поля опциональны;
     * ответ {items, total}: items — страница по 8 (без page — все подходящие), total — всего подходит.
     */
    public function filter(Request $request): JsonResponse
    {
        $data = $request->validate([
            'query' => ['nullable', 'string', 'max:100'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0'],
            'discounted' => ['nullable', 'boolean'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['string'],
            'manufacturers' => ['nullable', 'array'],
            'manufacturers.*' => ['integer'],
            'featured' => ['nullable', 'boolean'],
            'is_volume_price' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'in:default,price_asc,price_desc,name'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        // Эффективная цена: скидка, если есть (у тарифных товаров price уже равен дешёвому тарифу)
        $effectivePrice = 'COALESCE(discount_price, price)';

        $query = Product::active()
            ->when($data['query'] ?? null, fn (Builder $q, string $term) => $q
                ->where('name', 'ilike', '%'.addcslashes($term, '%_\\').'%'))
            ->when(isset($data['price_min']), fn (Builder $q) => $q
                ->whereRaw("{$effectivePrice} >= ?", [(int) round($data['price_min'] * 100)]))
            ->when(isset($data['price_max']), fn (Builder $q) => $q
                ->whereRaw("{$effectivePrice} <= ?", [(int) round($data['price_max'] * 100)]))
            // «Со скидкой»: скидка или объёмные тарифы; обычная цена без скидки не проходит
            ->when($data['discounted'] ?? false, fn (Builder $q) => $q
                ->where(fn (Builder $w) => $w->whereNotNull('discount_price')->orWhere('is_volume_price', true)))
            ->when($data['categories'] ?? null, function (Builder $q, array $slugs): void {
                // Слаг корневой категории включает товары её подкатегорий
                $q->whereIn('category_id', Category::query()
                    ->whereIn('slug', $slugs)
                    ->orWhereHas('parent', fn (Builder $p) => $p->whereIn('slug', $slugs))
                    ->pluck('id'));
            })
            ->when($data['manufacturers'] ?? null, fn (Builder $q, array $ids) => $q->whereIn('manufacturer_id', $ids))
            ->when(isset($data['featured']), fn (Builder $q) => $q->where('is_featured', $data['featured']))
            ->when(isset($data['is_volume_price']), fn (Builder $q) => $q->where('is_volume_price', $data['is_volume_price']));

        $total = (clone $query)->count();

        $items = $query
            ->with(['attributes', 'related'])
            ->when(($data['sort'] ?? 'default') === 'price_asc', fn (Builder $q) => $q->orderByRaw("{$effectivePrice} asc"))
            ->when(($data['sort'] ?? 'default') === 'price_desc', fn (Builder $q) => $q->orderByRaw("{$effectivePrice} desc"))
            ->when(($data['sort'] ?? 'default') === 'name', fn (Builder $q) => $q->orderBy('name'))
            ->orderBy('id')
            ->when(isset($data['page']), fn (Builder $q) => $q->forPage($data['page'], self::PER_PAGE))
            ->get();

        return response()->json([
            'items' => ProductResource::collection($items),
            'total' => $total,
        ]);
    }

    /** Блок «Популярные»: максимум Product::FEATURED_LIMIT товаров, без пагинации. */
    public function featured(): AnonymousResourceCollection
    {
        return ProductResource::collection(
            Product::active()
                ->where('is_featured', true)
                ->with(['attributes', 'related'])
                ->orderBy('id')
                ->take(Product::FEATURED_LIMIT)
                ->get(),
        );
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
