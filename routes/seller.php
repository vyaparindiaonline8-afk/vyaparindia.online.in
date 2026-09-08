<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\ProductController;

use App\Http\Controllers\Seller\OrderController;

use App\Http\Controllers\Seller\DispatchController;

use App\Http\Controllers\Seller\EnquiryController;

use App\Http\Controllers\Seller\SellerProfileController;

use App\Http\Controllers\Seller\MiniSiteController;

Route::middleware(['auth', 'is_seller'])->name('seller.')->prefix('seller')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('orders/{order}/dispatch/create', [DispatchController::class, 'create'])->name('orders.dispatch.create');
    Route::post('orders/{order}/dispatch', [DispatchController::class, 'store'])->name('orders.dispatch.store');
    Route::get('enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');
    Route::post('enquiries/{enquiry}/reply', [EnquiryController::class, 'reply'])->name('enquiries.reply');

    Route::get('profile/create', [SellerProfileController::class, 'create'])->name('profile.create');
    Route::post('profile', [SellerProfileController::class, 'store'])->name('profile.store');
    Route::get('profile/edit', [SellerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [SellerProfileController::class, 'update'])->name('profile.update');

    Route::get('minisite/create', [MiniSiteController::class, 'create'])->name('minisite.create');
    Route::post('minisite', [MiniSiteController::class, 'store'])->name('minisite.store');
    Route::get('minisite/edit', [MiniSiteController::class, 'edit'])->name('minisite.edit');
    Route::put('minisite', [MiniSiteController::class, 'update'])->name('minisite.update');

    // Dropshipper Routes
    Route::prefix('dropship')->name('dropship.')->group(function () {
        Route::get('/hub', [\App\Http\Controllers\Seller\DropshipController::class, 'hub'])->name('hub');
        Route::post('/import-single', [\App\Http\Controllers\Seller\DropshipController::class, 'importSingle'])->name('importSingle');
        Route::post('/import-bulk', [\App\Http\Controllers\Seller\DropshipController::class, 'importBulk'])->name('importBulk');
        Route::get('/my-products', [\App\Http\Controllers\Seller\DropshipController::class, 'myProducts'])->name('myProducts');
        Route::get('/orders', [\App\Http\Controllers\Seller\DropshipController::class, 'orders'])->name('orders');
        Route::post('/orders/{dsOrder}/approve', [\App\Http\Controllers\Seller\DropshipController::class, 'approvePriceAdjustment'])->name('approvePrice');
        Route::post('/orders/{dsOrder}/reject', [\App\Http\Controllers\Seller\DropshipController::class, 'rejectPriceAdjustment'])->name('rejectPrice');
        Route::get('/wallet', [\App\Http\Controllers\Seller\DropshipController::class, 'wallet'])->name('wallet');
    });

    // Wholesaler Supplier Fulfillment Routes
    Route::prefix('wholesaler')->name('wholesaler.')->group(function () {
        Route::get('/orders', [\App\Http\Controllers\Seller\WholesalerFulfillmentController::class, 'orders'])->name('orders');
        Route::post('/orders/{dsOrder}/adjust', [\App\Http\Controllers\Seller\WholesalerFulfillmentController::class, 'adjustPricing'])->name('adjustPricing');
        Route::post('/orders/{dsOrder}/accept', [\App\Http\Controllers\Seller\WholesalerFulfillmentController::class, 'acceptOrder'])->name('acceptOrder');
        Route::post('/orders/{dsOrder}/dispatch', [\App\Http\Controllers\Seller\WholesalerFulfillmentController::class, 'markDispatched'])->name('markDispatched');
        Route::post('/orders/{dsOrder}/deliver', [\App\Http\Controllers\Seller\WholesalerFulfillmentController::class, 'markDelivered'])->name('markDelivered');
        Route::post('/orders/{dsOrder}/cod-status', [\App\Http\Controllers\Seller\WholesalerFulfillmentController::class, 'updateCodStatus'])->name('updateCodStatus');
        Route::get('/orders/{dsOrder}/invoice', [\App\Http\Controllers\Seller\WholesalerFulfillmentController::class, 'invoice'])->name('invoice');
    });

    // Multi-Channel E-Commerce & AI Listing Routes
    Route::prefix('channels')->name('channels.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Seller\ChannelController::class, 'index'])->name('index');
        Route::post('/store', [\App\Http\Controllers\Seller\ChannelController::class, 'storeChannel'])->name('store');
        Route::post('/{channel}/toggle', [\App\Http\Controllers\Seller\ChannelController::class, 'toggleChannel'])->name('toggle');
        Route::post('/ai-generate', [\App\Http\Controllers\Seller\ChannelController::class, 'aiGenerate'])->name('aiGenerate');
        Route::get('/publisher', [\App\Http\Controllers\Seller\ChannelController::class, 'publisher'])->name('publisher');
        Route::post('/publish', [\App\Http\Controllers\Seller\ChannelController::class, 'publishProduct'])->name('publish');
        Route::get('/inventory', [\App\Http\Controllers\Seller\ChannelController::class, 'inventory'])->name('inventory');
        Route::post('/products/{product}/sync-stock', [\App\Http\Controllers\Seller\ChannelController::class, 'syncStock'])->name('syncStock');
    });

    // Smart Shipping, Multi-Carrier Rates & Anti-RTO Routes
    Route::prefix('shipping')->name('shipping.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Seller\SmartShippingController::class, 'index'])->name('index');
        Route::post('/estimate-rates', [\App\Http\Controllers\Seller\SmartShippingController::class, 'estimateRates'])->name('estimateRates');
        Route::get('/thermal-labels', [\App\Http\Controllers\Seller\SmartShippingController::class, 'bulkThermalLabels'])->name('thermalLabels');
        Route::get('/manifest', [\App\Http\Controllers\Seller\SmartShippingController::class, 'bulkManifest'])->name('manifest');
        Route::get('/orders/{order}/send-verification', [\App\Http\Controllers\Seller\SmartShippingController::class, 'sendVerification'])->name('sendVerification');
        Route::post('/ndr/{dsOrder}', [\App\Http\Controllers\Seller\SmartShippingController::class, 'handleNdr'])->name('handleNdr');
    });
});
