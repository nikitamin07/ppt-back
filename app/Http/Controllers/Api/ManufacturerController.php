<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ManufacturerResource;
use App\Models\Manufacturer;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ManufacturerController extends Controller
{
    /**
     * Список производителей для фильтра каталога (id -> POST /products/filter manufacturers[]).
     * Только те, у кого есть активные товары: иначе фильтр по ним даёт пустую выдачу.
     */
    public function index(): AnonymousResourceCollection
    {
        return ManufacturerResource::collection(
            Manufacturer::whereHas('products', fn ($query) => $query->active())
                ->orderBy('name')
                ->get(),
        );
    }
}
