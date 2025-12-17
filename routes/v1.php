<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\WishlistController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('product')->name('product.')->group(function () {
        Route::get('/', [ProductController::class, 'fetchAll'])->name('fetch-all');
        Route::get('/{product}', [ProductController::class, 'fetch'])->name('fetch');
    });

    Route::prefix('wishlist')->name('wishlist.')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('fetch');
        Route::post('/{product}', [WishlistController::class, 'addProduct'])->name('add');
        Route::delete('/{product}', [WishlistController::class, 'removeProduct'])->name('remove');
    });
});
