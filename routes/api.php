<?php

use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;

// Mendaftarkan satu resource route untuk API CRUD Service secara otomatis
Route::apiResource('services', ServiceController::class);