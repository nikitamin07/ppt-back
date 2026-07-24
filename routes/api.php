<?php

use App\Http\Controllers\Api\CallbackController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ManufacturerController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SiteVisitController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function (): void {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/count', [CategoryController::class, 'count']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);
    Route::get('/categories/{slug}/meta', [CategoryController::class, 'meta']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/count', [ProductController::class, 'count']);
    Route::get('/products/featured', [ProductController::class, 'featured']);
    Route::post('/products/filter', [ProductController::class, 'filter']);
    Route::get('/products/{categorySlug}/{productSlug}', [ProductController::class, 'show']);
    Route::get('/products/{categorySlug}/{productSlug}/meta', [ProductController::class, 'meta']);

    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/latest', [PostController::class, 'latest']);
    Route::get('/posts/{slug}', [PostController::class, 'show']);

    Route::get('/tags', [TagController::class, 'index']);

    Route::get('/manufacturers', [ManufacturerController::class, 'index']);

    // Отдельный лимит с отдельным префиксом ключа: у ThrottleRequests ключ = "prefix.domain|ip" без учёта
    // самих значений лимита, так что без префикса этот лимит делил бы счётчик с общим throttle:60,1
    // группы (тот же ключ) и удваивал бы инкремент на каждый запрос к /callback.
    Route::post('/callback', [CallbackController::class, 'store'])->middleware('throttle:10,1,callback');
    
    // Свой префикс ключа по той же причине
    Route::post('/comments', [CommentController::class, 'store'])->middleware('throttle:5,1,comments');

    Route::post('/track-visit', [SiteVisitController::class, 'store']);
});
