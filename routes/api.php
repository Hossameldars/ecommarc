<?php

use App\Http\Controllers\AuthController\EmailVerifiedController;
use App\Http\Controllers\AuthController\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::apiResource('products',ProductController::class);
Route::post('/register', [UserController::class, 'register']);
Route::post('/login',    [UserController::class, 'login']);

Route::post('/verifyEmail',    [EmailVerifiedController::class, 'verifyEmail']);
Route::post('/resendOtp',    [EmailVerifiedController::class, 'resendOtp']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
});
Route::get('Allcategory',[CategoryController::class,'index']);

    Route::get('/cart',          [CartController::class, 'index']);
    Route::post('/cart',         [CartController::class, 'store']);
    Route::put('/cart/{id}',     [CartController::class, 'update']);
    Route::delete('/cart/{id}',  [CartController::class, 'destroy']);
    Route::delete('/cart',       [CartController::class, 'clear']);
////////////////////////////

    Route::get('/orders',             [OrderController::class, 'index']);
    Route::get('/orders/{id}',        [OrderController::class, 'show']);
    Route::post('/orders',            [OrderController::class, 'store']);
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel']);
