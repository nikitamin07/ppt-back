<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function (): void {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/count', [CategoryController::class, 'count']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/count', [ProductController::class, 'count']);
    Route::get('/products/{categorySlug}/{productSlug}', [ProductController::class, 'show']);

    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{slug}', [PostController::class, 'show']);

    Route::get('/tags', [TagController::class, 'index']);
});
