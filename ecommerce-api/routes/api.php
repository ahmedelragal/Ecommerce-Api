<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Order\VendorOrderController;
use App\Http\Controllers\Api\Payment\PaymentController;
use App\Http\Controllers\Api\Profile\PasswordController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Product\CategoryController;
use App\Http\Controllers\Api\Product\ProductImageController;
use App\Http\Controllers\Api\Product\TagController;
use App\Http\Controllers\Api\Review\ReviewController;
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

    // Product Management Routes
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store'])->middleware('can:manage products');
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update'])->middleware('can:manage products');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->middleware('can:manage products');

    //Product Image Management Routes
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

    //Customer Order Management Routes
    Route::post('/orders', [OrderController::class, 'store'])->middleware('can:manage orders');
    Route::get('/orders', [OrderController::class, 'index'])->middleware('can:manage orders');
    Route::delete('/orders/{id}', [OrderController::class, 'cancel'])->middleware('can:manage orders');
    Route::get('/orders/{orderId}/track', [OrderController::class, 'track'])->middleware('can:manage orders');

    //Vendor Order Management Routes
    Route::get('/vendor/orders', [VendorOrderController::class, 'index'])->middleware('can:vendor orders');
    Route::patch('/vendor/orders/{orderId}/status', [VendorOrderController::class, 'updateStatus'])->middleware('can:vendor orders');
    Route::patch('/vendor/orders/{orderId}/items-status', [VendorOrderController::class, 'updateOrderItemStatus'])->middleware('can:vendor orders');

    //Payment Routes
    Route::post('/payment/create/{orderId}', [PaymentController::class, 'createPaymentIntent'])->middleware('can:manage orders');
    Route::post('/payment/confirm/{orderId}/{paymentIntentId}', [PaymentController::class, 'confirmPayment'])->middleware('can:manage orders');

    //Review and Rating Routes
    Route::post('products/{id}/review', [ReviewController::class, 'submitReview'])->middleware('can:write reviews');
    Route::put('reviews/{id}/approve', [ReviewController::class, 'approveReview'])->middleware('can:approve reviews');
    Route::get('products/{id}/reviews', [ReviewController::class, 'getProductReviews']);
});
