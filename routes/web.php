<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    return view('home');
})->name('home');

// Authentication Routes
Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register']);
    });

   
    Route::middleware('auth')->group(function () {
        Route::get('/', [AuthController::class, 'dashboard'])->name('index');
        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});

// Admin Routes
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Users Management
    // Route::resource('users', AdminController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::match(['put','patch'], '/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.destroy');
    
    // Categories Management
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
    Route::get('/categories/create', [AdminController::class, 'createCategory'])->name('categories.create');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::get('/categories/{id}/edit', [AdminController::class, 'editCategory'])->name('categories.edit');
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory'])->name('categories.delete');
    
    // Products Management
    Route::get('/product', [AdminController::class, 'products'])->name('products.index');
    Route::get('/product/create', [AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/product', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::get('/product/{id}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
    Route::put('/product/{id}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/product/{id}', [AdminController::class, 'deleteProduct'])->name('products.delete');
    

    //Brands Management
    Route::get('/brands', [AdminController::class, 'brands'])->name('brands.index');
    Route::get('/brands/create', [AdminController::class, 'createBrand'])->name('brands.create');
    Route::post('/brands', [AdminController::class, 'storeBrand'])->name('brands.store');
    Route::get('/brands/{id}/edit', [AdminController::class, 'editBrand'])->name('brands.edit');
    Route::put('/brands/{id}', [AdminController::class, 'updateBrand'])->name('brands.update');
    Route::delete('/brands/{id}', [AdminController::class, 'deleteBrand'])->name('brands.delete');
    
    // Orders Management
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::put('/orders/{id}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.update-status');

    Route::resource('banner', BannerController::class)->except(['show']);
    Route::resource('coupon', CouponController::class)->except(['show']);
    Route::resource('post', PostController::class)->except(['show']);
    Route::resource('comment', CommentController::class)->only(['index','edit','update','destroy']);
});

// // Admin resource routes for banners, coupons, posts, comments
// Route::middleware(['auth','admin'])->group(function () {
    
// });
