<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Buyer\DashboardController;
use App\Http\Controllers\Buyer\OrderController;

use App\Http\Controllers\Buyer\DispatchViewController;

use App\Http\Controllers\Buyer\EnquiryController;

Route::middleware(['auth', 'is_buyer'])->name('buyer.')->prefix('buyer')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('orders', OrderController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('orders/{order}/dispatch', [DispatchViewController::class, 'show'])->name('orders.dispatch.show');
    Route::get('products/{product}/enquiry/create', [EnquiryController::class, 'create'])->name('products.enquiry.create');
    Route::post('products/{product}/enquiry', [EnquiryController::class, 'store'])->name('products.enquiry.store');
});
