<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class CategoryController extends Controller
{
    /** Дерево: корневые категории с детьми. */
    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection(
            Category::query()
                ->whereNull('parent_id')
                ->with(['children' => fn ($query) => $query->orderBy('id')])
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
                ->with(['children' => fn ($query) => $query->orderBy('id')])
                ->firstOrFail(),
        );
    }
}
