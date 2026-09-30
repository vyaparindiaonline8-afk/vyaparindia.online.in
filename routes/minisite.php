<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seller\MiniSiteController;

Route::prefix('{sellerPage:slug}')->name('minisite.')->group(function () {
    Route::get('/', [MiniSiteController::class, 'show'])->name('show');
    Route::get('/products', [MiniSiteController::class, 'products'])->name('products');
    Route::get('/product/{productSlug}', [MiniSiteController::class, 'product'])->name('product');
    Route::post('/product/{productSlug}/review', [MiniSiteController::class, 'submitReview'])->name('submitReview');
    Route::get('/contact', [MiniSiteController::class, 'contact'])->name('contact');
    Route::get('/checkout', [MiniSiteController::class, 'checkout'])->name('checkout');
    Route::post('/order', [MiniSiteController::class, 'placeOrder'])->name('placeOrder');
    Route::post('/quick-order', [MiniSiteController::class, 'quickOrder'])->name('quickOrder');
    Route::get('/order-success/{order}', [MiniSiteController::class, 'orderSuccess'])->name('orderSuccess');

    // 🛠️ Hardware & Plumber Material Slip AI Scanner Routes
    Route::get('/material-scanner', [MiniSiteController::class, 'materialScannerView'])->name('materialScanner');
    Route::post('/material-scanner', [MiniSiteController::class, 'processMaterialSlip'])->name('processMaterialSlip');
    Route::get('/material-scanner/download-excel', [MiniSiteController::class, 'downloadQuotationExcel'])->name('downloadQuotationExcel');
});