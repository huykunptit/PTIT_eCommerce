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
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\VNPayController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\DB;
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

// Static pages
Route::get('/about', function(){
    return view('about');
})->name('about');

Route::get('/contact', function(){
    return view('contact');
})->name('contact');

// Search
Route::get('/search', function (\Illuminate\Http\Request $request) {
    $query = $request->get('q', '');
    $categoryId = $request->get('category', '');
    
    $products = DB::table('products')
        ->where('status', 'active')
        ->where(function($q) use ($query) {
            if ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            }
        });
    
    if ($categoryId) {
        $products->where('category_id', $categoryId);
    }
    
    $products = $products->limit(10)->get();
    
    return response()->json($products);
})->name('search');

// Product detail
Route::get('/product/{id}', function ($id) {
    return view('product.show', ['id' => $id]);
})->name('product.show');

// Cart Routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::put('/update/{key}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{key}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/data', [CartController::class, 'getCartData'])->name('data');
});

// Wishlist Routes
Route::prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('index');
    Route::post('/add', [WishlistController::class, 'add'])->name('add');
    Route::delete('/remove/{productId}', [WishlistController::class, 'remove'])->name('remove');
    Route::delete('/clear', [WishlistController::class, 'clear'])->name('clear');
    Route::get('/data', [WishlistController::class, 'getWishlistData'])->name('data');
});

// Password Reset Routes (outside auth group for simpler route names)
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'resetPassword'])->name('password.update');
});

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

// User Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'showUserProfile'])->name('user.profile');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'updateUserProfile'])->name('user.profile.update');
    Route::get('/orders', [\App\Http\Controllers\ProfileController::class, 'showUserOrders'])->name('user.orders');
    
    // Order Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/recent', [\App\Http\Controllers\OrderController::class, 'getRecentOrders'])->name('recent');
        Route::get('/{id}', [\App\Http\Controllers\OrderController::class, 'show'])->name('show');
        Route::post('/{id}/cancel', [\App\Http\Controllers\OrderController::class, 'cancel'])->name('cancel');
        Route::post('/{id}/return', [\App\Http\Controllers\OrderController::class, 'return'])->name('return');
    });
});

// Checkout Routes
Route::middleware('auth')->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::get('/success', [CheckoutController::class, 'success'])->name('success');
});

// Payment Routes
Route::prefix('payment')->name('payment.')->group(function () {
    // VNPay Routes
    Route::prefix('vnpay')->name('vnpay.')->group(function () {
        Route::match(['get', 'post'], '/create', [VNPayController::class, 'createPayment'])->name('create');
        Route::get('/return', [VNPayController::class, 'return'])->name('return');
        Route::post('/ipn', [VNPayController::class, 'ipn'])->name('ipn');
        Route::get('/ipn', [VNPayController::class, 'ipn'])->name('ipn.get'); // VNPay có thể gọi GET hoặc POST
    });
});

// Admin Routes
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Admin Profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'showAdminProfile'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'updateAdminProfile'])->name('profile.update');
    
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
    Route::put('/orders/{id}/shipping-status', [AdminController::class, 'updateShippingStatus'])->name('orders.update-shipping-status');
    Route::post('/orders/{id}/cancellation', [AdminController::class, 'handleCancellation'])->name('orders.handle-cancellation');
    Route::post('/orders/{id}/return', [AdminController::class, 'handleReturn'])->name('orders.handle-return');

    Route::resource('banner', BannerController::class)->except(['show']);
    Route::resource('coupon', CouponController::class)->except(['show']);
    Route::resource('post', PostController::class)->except(['show']);
    Route::resource('comment', CommentController::class)->only(['index','edit','update','destroy']);
    Route::resource('roles', RoleController::class)->except(['show'])->names('roles');

    // System Settings
    Route::get('/settings/system', [SystemSettingController::class, 'edit'])->name('system_settings.edit');
    Route::post('/settings/system', [SystemSettingController::class, 'update'])->name('system_settings.update');
});

// Admin Brand routes with URL prefix but route names without the admin. prefix to match existing views
Route::group(['prefix' => 'admin', 'middleware' => ['auth','admin']], function () {
   
});

// // Admin resource routes for banners, coupons, posts, comments
// Route::middleware(['auth','admin'])->group(function () {
    
// });
