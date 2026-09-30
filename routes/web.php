<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{product:slug}', [HomeController::class, 'showProduct'])->name('product.show');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/search-sellers-by-city', [HomeController::class, 'searchSellersByCity'])->name('search_sellers_by_city');
Route::view('/how-it-works/algorithm', 'public.algorithm')->name('marketplace.algorithm');

// Public WhatsApp Order Verification & Instant Prepaid Conversion Routes
Route::prefix('order-verify')->name('verification.')->group(function () {
    Route::get('/{token}', [\App\Http\Controllers\OrderVerificationController::class, 'show'])->name('show');
    Route::post('/{token}/pay', [\App\Http\Controllers\OrderVerificationController::class, 'payPrepaid'])->name('pay');
    Route::post('/{token}/confirm-cod', [\App\Http\Controllers\OrderVerificationController::class, 'confirmCod'])->name('confirmCod');
});

// Auth routes are in routes/auth.php
// Buyer routes are in routes/buyer.php
// Seller routes are in routes/seller.php
// Admin routes are in routes/admin.php

require __DIR__.'/auth.php';
require __DIR__.'/buyer.php';
require __DIR__.'/seller.php';
require __DIR__.'/admin.php';
require __DIR__.'/minisite.php';

