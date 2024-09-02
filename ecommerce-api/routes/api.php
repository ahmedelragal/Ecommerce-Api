<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Profile\PasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['throttle:3,1'])->group(function () {
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
    Route::middleware(['role:admin'])->group(function () {});

    // Routes for Vendor
    Route::middleware(['role:vendor'])->group(function () {});

    // Routes for Customer
    Route::middleware(['role:customer'])->group(function () {});
});
