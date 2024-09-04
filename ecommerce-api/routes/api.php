<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Profile\PasswordController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Product\CategoryController;
use App\Http\Controllers\Api\Product\ProductImageController;
use App\Http\Controllers\Api\Product\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// User Auth Routes
Route::middleware(['throttle:20,1'])->group(function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/verify_email', [AuthController::class, 'verifyUserEmail']);
});
Route::middleware(['throttle:1,1'])->group(function () {
    Route::post('auth/resend_email', [AuthController::class, 'resendEmail']);
});

Route::middleware(['auth'])->group(function () {
    Route::post('/change_password', [PasswordController::class, 'changePassword']);

    // Routes for Admin
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/test', function () {
            return 'admin test';
        });
    });

    // Product Management Routes
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store'])->middleware('can:manage products');
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update'])->middleware('can:manage products');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->middleware('can:manage products');
    //Product Image Management
    Route::post('products/{id}/images', [ProductImageController::class, 'upload'])->middleware('can:manage products');
    Route::delete('products/{productId}/images/{imageId}', [ProductImageController::class, 'delete'])->middleware('can:manage products');
    Route::get('products/{id}/images', [ProductImageController::class, 'getImages'])->middleware('can:manage products');

    //Category Management Routes
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store'])->middleware('can:manage categories');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->middleware('can:manage categories');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->middleware('can:manage categories');
    Route::get('/categories/{id}', [CategoryController::class, 'show']);

    //Tag Management Routes
    Route::get('/tags', [TagController::class, 'index']);
    Route::post('/tags', [TagController::class, 'store'])->middleware('can:manage tags');
    Route::put('/tags/{id}', [TagController::class, 'update'])->middleware('can:manage tags');
    Route::delete('/tags/{id}', [TagController::class, 'destroy'])->middleware('can:manage tags');
    Route::get('/tags/{id}', [TagController::class, 'show']);


    // // Routes for Customer
    // Route::middleware(['role:customer'])->group(function () {
    //     Route::post('/products', [ProductController::class, 'index']);
    //     Route::post('/products/{id}', [ProductController::class, 'show']);
    // });
});
