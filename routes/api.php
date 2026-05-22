<?php

use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::apiResource('services', ServiceController::class);
Route::apiResource('subscriptions', SubscriptionController::class);