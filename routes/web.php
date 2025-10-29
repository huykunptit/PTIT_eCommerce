<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProductController;
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
    Route::get('/users', [UserController::class, 'users'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'createUser'])->name('users.create');
    Route::post('/users', [UserController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'editUser'])->name('users.edit');
    Route::match(['put','patch'], '/users/{id}', [UserController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'deleteUser'])->name('users.destroy');
    
    // Categories Management
    Route::get('/categories', [CategoryController::class, 'categories'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'createCategory'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'storeCategory'])->name('categories.store');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'editCategory'])->name('categories.edit');
    Route::put('/categories/{id}', [CategoryController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'deleteCategory'])->name('categories.delete');
    
    // Products Management
    Route::get('/product', [ProductController::class, 'products'])->name('products.index');
    Route::get('/product/create', [ProductController::class, 'createProduct'])->name('products.create');
    Route::post('/product', [ProductController::class, 'storeProduct'])->name('products.store');
    Route::get('/product/{id}/edit', [ProductController::class, 'editProduct'])->name('products.edit');
    Route::put('/product/{id}', [ProductController::class, 'updateProduct'])->name('products.update');
    Route::delete('/product/{id}', [ProductController::class, 'deleteProduct'])->name('products.delete');
    

    // Brands Management (resourceful, names: brand.* to match views)
    Route::resource('brands', BrandController::class)->except(['show'])->names('brands  ');
    Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
    Route::get('/brands/create', [BrandController::class, 'create'])->name('brands.create');
    Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
    Route::get('/brands/{id}/edit', [BrandController::class, 'edit'])->name('brands.edit');
    Route::put('/brands/{id}', [BrandController::class, 'update'])->name('brands.update');
    Route::delete('/brands/{id}', [BrandController::class, 'destroy'])->name('brands.destroy');

    // Orders Management
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::put('/orders/{id}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.update-status');

    Route::resource('banner', BannerController::class)->except(['show']);
    Route::resource('coupon', CouponController::class)->except(['show']);
    Route::resource('post', PostController::class)->except(['show']);
    Route::resource('comment', CommentController::class)->only(['index','edit','update','destroy']);
    Route::resource('roles', RoleController::class)->except(['show'])->names('roles');
});

// Admin Brand routes with URL prefix but route names without the admin. prefix to match existing views
Route::group(['prefix' => 'admin', 'middleware' => ['auth','admin']], function () {
   
});

// // Admin resource routes for banners, coupons, posts, comments
// Route::middleware(['auth','admin'])->group(function () {
    
// });
