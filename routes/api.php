<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('logout', [AuthController::class, 'logout']);

    Route::post('customers/{id}/activate', [CustomerController::class, 'activate']);
    Route::post('customers/{id}/restore', [CustomerController::class, 'restore']);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('services', ServiceController::class);
    Route::apiResource('subscriptions', SubscriptionController::class);
    Route::apiResource('invoices', InvoiceController::class);
    
});