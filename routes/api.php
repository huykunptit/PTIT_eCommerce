<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\HomepageController;
use App\Http\Controllers\Api\PublicProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Api\OrderApiController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/ping', function () {
    return response()->json(['status' => 'ok']);
});

// Public Homepage config/data
Route::get('/homepage', [HomepageController::class, 'index']);

// Public products search for chatbot/frontends
Route::get('/products', [PublicProductController::class, 'index']);

// API Authentication Routes
Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
    // Guest routes
    Route::post('/login', [AuthController::class, 'apiLogin']);
    Route::post('/register', [AuthController::class, 'apiRegister']);
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/logout', [AuthController::class, 'apiLogout']);
        Route::get('/profile', [AuthController::class, 'apiProfile']);
    });
});

// Chatbot API Routes (tạm thời mở cho cả guest; nếu cần có thể thêm auth:sanctum sau)
Route::group(['prefix' => 'chatbot', 'as' => 'chatbot.'], function () {
    Route::post('/message', [\App\Http\Controllers\ChatbotController::class, 'sendMessage']);
    Route::get('/system-data', [\App\Http\Controllers\ChatbotController::class, 'index']);
});

// Admin API Routes
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth:sanctum', 'api.admin']], function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/statistics', [AdminController::class, 'getStatistics']);
    
    // Users Management
    Route::get('/users', [AdminController::class, 'getUsers']);
    Route::get('/users/{id}', [AdminController::class, 'getUser']);
    Route::post('/users', [AdminController::class, 'createUser']);
    Route::put('/users/{id}', [AdminController::class, 'updateUser']);
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
    
    // Categories Management
    Route::get('/categories', [AdminController::class, 'getCategories']);
    Route::get('/categories/{id}', [AdminController::class, 'getCategory']);
    Route::post('/categories', [AdminController::class, 'createCategory']);
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory']);
    Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory']);
    
    // Products Management
    Route::get('/products', [AdminController::class, 'getProducts']);
    Route::get('/products/{id}', [AdminController::class, 'getProduct']);
    Route::post('/products', [AdminController::class, 'createProduct']);
    Route::put('/products/{id}', [AdminController::class, 'updateProduct']);
    Route::delete('/products/{id}', [AdminController::class, 'deleteProduct']);
    
    // Orders Management
    Route::get('/orders', [AdminController::class, 'getOrders']);
    Route::get('/orders/{id}', [AdminController::class, 'getOrder']);
    Route::put('/orders/{id}/status', [AdminController::class, 'updateOrderStatus']);

    // Fine-grained permissions for employees
    Route::get('/employees/{id}/permissions', [AdminController::class, 'getEmployeePermissions']);
    Route::put('/employees/{id}/permissions', [AdminController::class, 'updateEmployeePermissions']);
});

// Public / session-based Cart API (giữ nguyên logic giỏ hàng hiện tại)
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'getCartData']);
    Route::post('/items', [CartController::class, 'add']);
    Route::put('/items/{key}', [CartController::class, 'update']);
    Route::delete('/items/{key}', [CartController::class, 'remove']);
    Route::delete('/', [CartController::class, 'clear']);
});

// User Orders REST API (yêu cầu auth:sanctum)
Route::middleware('auth:sanctum')->prefix('orders')->group(function () {
    Route::get('/', [OrderApiController::class, 'index']);
    Route::get('/{id}', [OrderApiController::class, 'show']);
    Route::post('/', [OrderApiController::class, 'store']);
    Route::delete('/{id}', [OrderApiController::class, 'cancel']);
});
