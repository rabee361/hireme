<?php

use App\Http\Controllers\Api\AdController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('signup', [AuthController::class, 'signup']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('send-email', [AuthController::class, 'sendEmail']);
    Route::post('change-password', [AuthController::class, 'changePassword']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    Route::middleware(['auth:api', 'jwt.access'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::apiResource('ads', AdController::class)->only(['index', 'show']);
Route::apiResource('projects', ProjectController::class)->only(['index', 'show']);

Route::middleware(['auth:api', 'jwt.access'])->group(function () {
    Route::apiResource('students', StudentController::class)->only(['index', 'show']);
    Route::apiResource('companies', CompanyController::class)->only(['index', 'show']);
    Route::apiResource('customers', CustomerController::class)->only(['index', 'show']);

    Route::apiResource('ads', AdController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('projects', ProjectController::class)->only(['store', 'update', 'destroy']);
});