<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryListResource;
use App\Http\Resources\CategoryMetaResource;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class CategoryController extends Controller
{
    /** Дерево: корневые категории с детьми, в порядке, заданном в админке. Без описаний — они нужны только на странице категории. */
    public function index(): AnonymousResourceCollection
    {
        return CategoryListResource::collection(
            Category::query()
                ->whereNull('parent_id')
                ->with('children')
                ->orderBy('position')
                ->orderBy('id')
                ->get(),
        );
    }

    public function count(): JsonResponse
    {
        return response()->json(['count' => Category::count()]);
    }

    public function show(string $slug): CategoryResource
    {
        return new CategoryResource(
            Category::query()
                ->where('slug', $slug)
                ->with('children')
                ->firstOrFail(),
        );
    }

    /** Для generateMetadata на фронте: без описания, детей и прочего — только теги страницы. */
    public function meta(string $slug): CategoryMetaResource
    {
        return new CategoryMetaResource(
            Category::query()->where('slug', $slug)->firstOrFail(),
        );
    }
}
