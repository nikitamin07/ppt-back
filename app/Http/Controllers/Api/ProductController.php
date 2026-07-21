<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\ProductMetaResource;
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

    /** Связи, из которых ProductListResource собирает карточку. */
    private const CARD_RELATIONS = ['category', 'manufacturer'];

    /** Скидка, если есть; у тарифных товаров price уже равен самому дешёвому тарифу. */
    private const EFFECTIVE_PRICE = 'COALESCE(discount_price, price)';

    /** Всегда постранично по 8: без ?page — первая страница (всего товаров — /products/count). */
    public function index(Request $request): AnonymousResourceCollection
    {
        $products = $this->filtered($request)
            ->with(self::CARD_RELATIONS)
            ->orderBy('id')
            ->forPage(self::page($request), self::PER_PAGE)
            ->get();

        return ProductListResource::collection($products);
    }

    public function count(Request $request): JsonResponse
    {
        return response()->json(['count' => $this->filtered($request)->count()]);
    }

    /**
     * Страница каталога с фильтрами. JSON-тело, все поля опциональны;
     * ответ {items, total}: items — страница по 8 (как в index), total — всего подходит.
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

        $query = Product::active()
            ->when($data['query'] ?? null, fn (Builder $q, string $term) => $q->search($term))
            ->when(isset($data['price_min']), fn (Builder $q) => $q
                ->whereRaw(self::EFFECTIVE_PRICE.' >= ?', [(int) round($data['price_min'] * 100)]))
            ->when(isset($data['price_max']), fn (Builder $q) => $q
                ->whereRaw(self::EFFECTIVE_PRICE.' <= ?', [(int) round($data['price_max'] * 100)]))
            // «Со скидкой»: скидка или объёмные тарифы; обычная цена без скидки не проходит
            ->when($data['discounted'] ?? false, fn (Builder $q) => $q
                ->where(fn (Builder $w) => $w->whereNotNull('discount_price')->orWhere('is_volume_price', true)))
            ->when($data['categories'] ?? null, fn (Builder $q, array $slugs) => $q
                ->whereIn('category_id', Category::idsBySlugs($slugs)))
            ->when($data['manufacturers'] ?? null, fn (Builder $q, array $ids) => $q->whereIn('manufacturer_id', $ids))
            ->when(isset($data['featured']), fn (Builder $q) => $q->where('is_featured', $data['featured']))
            ->when(isset($data['is_volume_price']), fn (Builder $q) => $q->where('is_volume_price', $data['is_volume_price']));

        $total = (clone $query)->count();

        $items = $query
            ->with(self::CARD_RELATIONS)
            ->tap(fn (Builder $q) => match ($data['sort'] ?? 'default') {
                'price_asc' => $q->orderByRaw(self::EFFECTIVE_PRICE.' asc'),
                'price_desc' => $q->orderByRaw(self::EFFECTIVE_PRICE.' desc'),
                'name' => $q->orderBy('name'),
                default => $q,
            })
            ->orderBy('id')
            ->forPage($data['page'] ?? 1, self::PER_PAGE)
            ->get();

        return response()->json([
            'items' => ProductListResource::collection($items),
            'total' => $total,
        ]);
    }

    /** Блок «Популярные»: максимум Product::FEATURED_LIMIT товаров, без пагинации. */
    public function featured(): AnonymousResourceCollection
    {
        return ProductListResource::collection(
            Product::active()
                ->where('is_featured', true)
                ->with(self::CARD_RELATIONS)
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
            ->with([...self::CARD_RELATIONS, 'attributes', 'related'])
            ->firstOrFail();

        return new ProductResource($product);
    }

    /** Для generateMetadata на фронте: без описания и характеристик — только теги страницы. */
    public function meta(string $categorySlug, string $productSlug): ProductMetaResource
    {
        $product = Product::active()
            ->where('slug', $productSlug)
            ->whereRelation('category', 'slug', $categorySlug)
            ->with('category')
            ->firstOrFail();

        return new ProductMetaResource($product);
    }

    /** Номер страницы из ?page: мусор и отсутствие параметра дают первую. */
    private static function page(Request $request): int
    {
        return max(1, (int) $request->query('page', 1));
    }

    /** Общие фильтры списка и счётчика: ?category=slug (корень или подкатегория), ?query=поиск. */
    private function filtered(Request $request): Builder
    {
        return Product::active()
            ->when($request->query('category'), fn (Builder $query, string $slug) => $query
                ->whereIn('category_id', Category::idsBySlugs([$slug])))
            ->when($request->query('query'), fn (Builder $query, string $term) => $query->search($term));
    }
}
