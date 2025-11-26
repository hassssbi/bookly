<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SellerProfileController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/

// Public browsing
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{category}', [CategoryController::class, 'show']);
Route::get('books', [BookController::class, 'index']);
Route::get('books/{book}', [BookController::class, 'show']);

// Auth
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected API Routes (auth:sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Current user
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    /*
    |----------------------------------------------------------------------
    | Seller Routes (role: seller)
    |   Prefix: /seller/...
    |----------------------------------------------------------------------
    */
    Route::middleware('role:seller')
        ->prefix('seller')
        ->group(function () {
            // Seller profile: view + apply/update
            Route::get('profile', [SellerProfileController::class, 'showCurrent']);
            Route::post('profile', [SellerProfileController::class, 'store']);

            // Seller book management
            Route::apiResource('books', BookController::class)
                ->except(['index', 'show']);
        });

    /*
    |----------------------------------------------------------------------
    | Orders & Wishlist (any authenticated user)
    |----------------------------------------------------------------------
    */

    // Orders
    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{order}', [OrderController::class, 'show']);

    // Wishlist
    Route::get('wishlist', [WishlistController::class, 'index']);
    Route::post('wishlist', [WishlistController::class, 'store']);
    Route::delete('wishlist/{wishlist}', [WishlistController::class, 'destroy']);

    /*
    |----------------------------------------------------------------------
    | Admin Routes (role: admin)
    |   Prefix: /admin/...
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin')
        ->prefix('admin')
        ->group(function () {
            // Category management (admin)
            Route::apiResource('categories', CategoryController::class)
                ->except(['index', 'show']);

            // Seller management (admin)
            Route::get('sellers', [SellerProfileController::class, 'index']);
            Route::put('sellers/{sellerProfile}', [SellerProfileController::class, 'update']);
        });
});
