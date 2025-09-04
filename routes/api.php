<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


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

// API Authentication Routes
Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
    // Guest routes
    Route::post('/login', [AuthController::class, 'apiLogin']);
    Route::post('/register', [AuthController::class, 'apiRegister']);
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'apiLogout']);
        Route::get('/profile', [AuthController::class, 'apiProfile']);
    });
});
