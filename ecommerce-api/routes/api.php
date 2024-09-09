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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// User Auth Routes
Route::middleware(['throttle:10,1'])->group(function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/verify_email', [AuthController::class, 'verifyUserEmail']);
});
Route::middleware(['throttle:2,1'])->group(function () {
    Route::post('auth/resend_email', [AuthController::class, 'resendEmail']);
});

Route::middleware(['auth'])->group(function () {
    Route::post('/change_password', [PasswordController::class, 'changePassword'])->middleware(['throttle:3,1']);

    // Product Management Routes
    Route::get('/products', [ProductController::class, 'index'])->middleware(['throttle:5,1']);
    Route::post('/products', [ProductController::class, 'store'])->middleware('can:manage products')->middleware(['throttle:5,1']);
    Route::get('/products/view/{id}', [ProductController::class, 'show'])->middleware(['throttle:5,1']);
    Route::put('/products/{id}', [ProductController::class, 'update'])->middleware('can:manage products')->middleware(['throttle:5,1']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->middleware('can:manage products')->middleware(['throttle:5,1']);

    //Product Image Management Routes
    Route::post('products/{id}/images', [ProductImageController::class, 'upload'])->middleware('can:manage products')->middleware(['throttle:5,1']);
    Route::delete('products/{productId}/images/{imageId}', [ProductImageController::class, 'delete'])->middleware('can:manage products')->middleware(['throttle:15,1']);
    Route::get('products/{id}/images', [ProductImageController::class, 'getImages'])->middleware('can:manage products')->middleware(['throttle:5,1']);

    //Category Management Routes
    Route::get('/categories', [CategoryController::class, 'index'])->middleware(['throttle:5,1']);
    Route::post('/categories', [CategoryController::class, 'store'])->middleware('can:manage categories')->middleware(['throttle:5,1']);
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->middleware('can:manage categories')->middleware(['throttle:5,1']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->middleware('can:manage categories')->middleware(['throttle:5,1']);
    Route::get('/categories/{id}', [CategoryController::class, 'show'])->middleware(['throttle:5,1']);

    //Tag Management Routes
    Route::get('/tags', [TagController::class, 'index'])->middleware(['throttle:5,1']);
    Route::post('/tags', [TagController::class, 'store'])->middleware('can:manage tags')->middleware(['throttle:5,1']);
    Route::put('/tags/{id}', [TagController::class, 'update'])->middleware('can:manage tags')->middleware(['throttle:5,1']);
    Route::delete('/tags/{id}', [TagController::class, 'destroy'])->middleware('can:manage tags')->middleware(['throttle:5,1']);
    Route::get('/tags/{id}', [TagController::class, 'show'])->middleware(['throttle:5,1']);

    //Customer Order Management Routes
    Route::post('/orders', [OrderController::class, 'store'])->middleware('can:manage orders')->middleware(['throttle:5,1']);
    Route::get('/orders', [OrderController::class, 'index'])->middleware('can:manage orders')->middleware(['throttle:5,1']);
    Route::delete('/orders/{id}', [OrderController::class, 'cancel'])->middleware('can:manage orders')->middleware(['throttle:5,1']);
    Route::get('/orders/{orderId}/track', [OrderController::class, 'track'])->middleware('can:manage orders')->middleware(['throttle:5,1']);

    //Vendor Order Management Routes
    Route::get('/vendor/orders', [VendorOrderController::class, 'index'])->middleware('can:vendor orders')->middleware(['throttle:5,1']);
    Route::patch('/vendor/orders/{orderId}/status', [VendorOrderController::class, 'updateStatus'])->middleware('can:vendor orders')->middleware(['throttle:5,1']);
    Route::patch('/vendor/orders/{orderId}/items-status', [VendorOrderController::class, 'updateOrderItemStatus'])->middleware('can:vendor orders')->middleware(['throttle:5,1']);

    //Payment Routes
    Route::post('/payment/create/{orderId}', [PaymentController::class, 'createPaymentIntent'])->middleware('can:manage orders')->middleware(['throttle:5,1']);
    Route::post('/payment/confirm/{orderId}/{paymentIntentId}', [PaymentController::class, 'confirmPayment'])->middleware('can:manage orders')->middleware(['throttle:5,1']);

    //Review and Rating Routes
    Route::post('products/{id}/review', [ReviewController::class, 'submitReview'])->middleware('can:write reviews')->middleware(['throttle:5,1']);
    Route::put('reviews/{id}/approve', [ReviewController::class, 'approveReview'])->middleware('can:approve reviews')->middleware(['throttle:5,1']);
    Route::get('products/{id}/reviews', [ReviewController::class, 'getProductReviews'])->middleware(['throttle:5,1']);

    //Product Search and Filtering
    Route::get('/products/search', [ProductController::class, 'search'])->middleware(['throttle:20,1']);
});
