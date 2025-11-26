<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\SellerController as AdminSellerController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\WishlistController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Seller\ApplicationController as SellerApplicationController;
use App\Http\Controllers\Seller\BookController as SellerBookController;
use App\Http\Controllers\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Seller\ProfileController as SellerProfileController;
use App\Http\Controllers\Shop\BookController as ShopBookController;
use App\Http\Controllers\Shop\CategoryController as ShopCategoryController;
use Illuminate\Support\Facades\Route;

// Login/Register page (guest only)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

// Public shop
Route::get('/shop', [ShopBookController::class, 'index'])->name('shop.index');
Route::get('/shop/categories/{category:slug}', [ShopCategoryController::class, 'show'])
    ->name('shop.categories.show');

// Public book show page (customer-style hero view)
Route::get('/shop/books/{book:slug}', [ShopBookController::class, 'show'])->name('shop.books.show');

// Logout (auth only)
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Dashboard (protected)
Route::get('/', function () {
    return view('dashboard', [
        'stats' => [
            'new_orders' => 0,
            'total_books' => 0,
        ],
    ]);
})->middleware('auth')->name('dashboard');

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Users management
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // Keep this if you like quick inline role update:
        Route::patch('users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.updateRole');

        // Categories management
        Route::get('categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
        Route::post('categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::get('categories/{category}', [AdminCategoryController::class, 'show'])->name('categories.show');
        Route::get('categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
        Route::put('categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        // Seller approvals
        Route::get('sellers', [AdminSellerController::class, 'index'])->name('sellers.index');
        Route::get('sellers/{sellerProfile}', [AdminSellerController::class, 'show'])->name('sellers.show');
        Route::patch('sellers/{sellerProfile}', [AdminSellerController::class, 'updateStatus'])->name('sellers.updateStatus');
    });

Route::middleware(['auth', 'role:seller'])
    ->prefix('seller')
    ->name('seller.')
    ->group(function () {

        // Seller profile
        Route::get('profile', [SellerProfileController::class, 'show'])->name('profile.show');
        Route::get('profile/edit', [SellerProfileController::class, 'edit'])->name('profile.edit');
        Route::post('profile', [SellerProfileController::class, 'storeOrUpdate'])->name('profile.store');

        // Books CRUD for seller
        Route::get('books', [SellerBookController::class, 'index'])->name('books.index');
        Route::get('books/create', [SellerBookController::class, 'create'])->name('books.create');
        Route::post('books', [SellerBookController::class, 'store'])->name('books.store');
        Route::get('books/{book}', [SellerBookController::class, 'show'])->name('books.show');
        Route::get('books/{book}/edit', [SellerBookController::class, 'edit'])->name('books.edit');
        Route::put('books/{book}', [SellerBookController::class, 'update'])->name('books.update');
        Route::delete('books/{book}', [SellerBookController::class, 'destroy'])->name('books.destroy');

        // Seller orders (view only)
        Route::get('orders', [SellerOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [SellerOrderController::class, 'show'])->name('orders.show');
    });

Route::middleware(['auth'])->group(function () {
    // Customer applies to become seller
    Route::get('seller/apply', [SellerApplicationController::class, 'show'])->name('seller.apply.show');
    Route::post('seller/apply', [SellerApplicationController::class, 'submit'])->name('seller.apply.submit');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Customer orders (must be logged in & role = customer)
Route::middleware(['auth', 'role:customer'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {

        Route::get('orders', [CustomerOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [CustomerOrderController::class, 'show'])->name('orders.show');
        /* Route::post('orders', [CustomerOrderController::class, 'store'])->name('orders.store'); */

        // Checkout + payment
        Route::post('checkout/start', [CheckoutController::class, 'start'])->name('checkout.start');
        Route::get('checkout', [CheckoutController::class, 'show'])->name('checkout.show');
        Route::post('checkout/confirm', [CheckoutController::class, 'confirm'])->name('checkout.confirm');

        Route::get('payment/{order}', [PaymentController::class, 'show'])->name('payment.show');
        Route::post('payment/{order}/pay', [PaymentController::class, 'complete'])->name('payment.complete');
        Route::post('payment/{order}/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

        // Wishlist
        Route::get('wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
        Route::post('wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
        Route::delete('wishlist/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    });
