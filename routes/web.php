<?php

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\InvoiceController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers');
    Route::get('/services', [ServiceController::class, 'index'])->name('services');
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions');
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices');

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    // API Routes for CRUD (moved to web for session sharing)
    Route::prefix('api')->group(function () {
        Route::post('customers/{id}/restore', [CustomerController::class, 'restore']);
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('services', ServiceController::class);
        Route::apiResource('subscriptions', SubscriptionController::class);
        Route::apiResource('invoices', InvoiceController::class);
    });
});