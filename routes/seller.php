<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\OrderController;
use App\Http\Controllers\Seller\DispatchController;
use App\Http\Controllers\Seller\EnquiryController;
use App\Http\Controllers\Seller\SellerProfileController;
use App\Http\Controllers\Seller\MiniSiteController;
use App\Http\Controllers\Seller\DropshipController;
use App\Http\Controllers\Seller\WholesalerFulfillmentController;
use App\Http\Controllers\Seller\ChannelController;
use App\Http\Controllers\Seller\SmartShippingController;
use App\Http\Controllers\Seller\MarketingController;
use App\Http\Controllers\Seller\CustomDomainController;
use App\Http\Controllers\Seller\GstInvoiceController;
use App\Http\Controllers\Seller\PayoutController;
use App\Http\Controllers\Seller\CatalogIngestionController;
use App\Http\Controllers\Seller\DropshipPartnerController;
use App\Http\Controllers\Seller\PhotoStudioController;
use App\Http\Controllers\Seller\AnalyticsController;
use App\Http\Controllers\Seller\BrandMasterController;
use App\Http\Controllers\Seller\ProductSpreadsheetController;

Route::middleware(['auth', 'is_seller'])->name('seller.')->prefix('seller')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    
    // 📊 Excel / CSV Round-Trip Sync & Daily Rate Revision Sheet
    Route::get('products/export-sheet', [ProductSpreadsheetController::class, 'export'])->name('products.export_sheet');
    Route::post('products/import-sheet', [ProductSpreadsheetController::class, 'import'])->name('products.import_sheet');
    Route::post('products/assign-folder', [ProductSpreadsheetController::class, 'assignFolder'])->name('products.assign_folder');

    Route::resource('products', ProductController::class);
    Route::post('products/{product}/quick-image-update', [ProductController::class, 'quickImageUpdate'])->name('products.quick_image_update');
    Route::delete('products/{product}/images/{image}', [ProductController::class, 'deleteImage'])->name('products.images.destroy');
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

    // 🌐 Custom Domain Linking Routes
    Route::prefix('minisite/custom-domain')->name('minisite.custom_domain.')->group(function () {
        Route::get('/', [CustomDomainController::class, 'index'])->name('index');
        Route::post('/', [CustomDomainController::class, 'store'])->name('store');
        Route::post('/{customDomain}/verify', [CustomDomainController::class, 'verify'])->name('verify');
    });

    // 📦 Dropshipper Routes & UPI Payouts
    Route::prefix('dropship')->name('dropship.')->group(function () {
        Route::get('/hub', [DropshipController::class, 'hub'])->name('hub');
        Route::post('/import-single', [DropshipController::class, 'importSingle'])->name('importSingle');
        Route::post('/import-bulk', [DropshipController::class, 'importBulk'])->name('importBulk');
        Route::get('/my-products', [DropshipController::class, 'myProducts'])->name('myProducts');
        Route::get('/orders', [DropshipController::class, 'orders'])->name('orders');
        Route::post('/orders/{dsOrder}/approve', [DropshipController::class, 'approvePriceAdjustment'])->name('approvePrice');
        Route::post('/orders/{dsOrder}/reject', [DropshipController::class, 'rejectPriceAdjustment'])->name('rejectPrice');
        Route::get('/wallet', [DropshipController::class, 'wallet'])->name('wallet');
        
        // Instant UPI Payouts
        Route::get('/payout-settings', [PayoutController::class, 'settings'])->name('payouts.settings');
        Route::post('/payout-account', [PayoutController::class, 'storeAccount'])->name('payouts.storeAccount');
        Route::post('/payout-request', [PayoutController::class, 'requestPayout'])->name('payouts.request');
        
        // Partner Request
        Route::post('/request-partner', [DropshipPartnerController::class, 'requestPartnership'])->name('requestPartner');
    });

    // 🏢 Wholesaler Supplier Fulfillment Routes
    Route::prefix('wholesaler')->name('wholesaler.')->group(function () {
        Route::get('/orders', [WholesalerFulfillmentController::class, 'orders'])->name('orders');
        Route::post('/orders/{dsOrder}/adjust', [WholesalerFulfillmentController::class, 'adjustPricing'])->name('adjustPricing');
        Route::post('/orders/{dsOrder}/accept', [WholesalerFulfillmentController::class, 'acceptOrder'])->name('acceptOrder');
        Route::post('/orders/{dsOrder}/dispatch', [WholesalerFulfillmentController::class, 'markDispatched'])->name('markDispatched');
        Route::post('/orders/{dsOrder}/deliver', [WholesalerFulfillmentController::class, 'markDelivered'])->name('markDelivered');
        Route::post('/orders/{dsOrder}/cod-status', [WholesalerFulfillmentController::class, 'updateCodStatus'])->name('updateCodStatus');
        Route::get('/orders/{dsOrder}/invoice', [WholesalerFulfillmentController::class, 'invoice'])->name('invoice');

        // Wholesaler Dropship Partner Management
        Route::get('/partners', [DropshipPartnerController::class, 'wholesalerPartners'])->name('partners');
        Route::post('/partners/{partnership}/status', [DropshipPartnerController::class, 'updatePartnershipStatus'])->name('updatePartnership');
    });


    // 🤖 Multi-Channel E-Commerce & AI Listing Routes
    Route::prefix('channels')->name('channels.')->group(function () {
        Route::get('/', [ChannelController::class, 'index'])->name('index');
        Route::post('/store', [ChannelController::class, 'storeChannel'])->name('store');
        Route::post('/{channel}/toggle', [ChannelController::class, 'toggleChannel'])->name('toggle');
        Route::post('/ai-generate', [ChannelController::class, 'aiGenerate'])->name('aiGenerate');
        Route::get('/publisher', [ChannelController::class, 'publisher'])->name('publisher');
        Route::post('/publish', [ChannelController::class, 'publishProduct'])->name('publish');
        Route::get('/inventory', [ChannelController::class, 'inventory'])->name('inventory');
        Route::post('/products/{product}/sync-stock', [ChannelController::class, 'syncStock'])->name('syncStock');
    });

    // 🚚 Smart Shipping & Anti-RTO Routes
    Route::prefix('shipping')->name('shipping.')->group(function () {
        Route::get('/', [SmartShippingController::class, 'index'])->name('index');
        Route::post('/estimate-rates', [SmartShippingController::class, 'estimateRates'])->name('estimateRates');
        Route::get('/thermal-labels', [SmartShippingController::class, 'bulkThermalLabels'])->name('thermalLabels');
        Route::get('/manifest', [SmartShippingController::class, 'bulkManifest'])->name('manifest');
        Route::get('/orders/{order}/send-verification', [SmartShippingController::class, 'sendVerification'])->name('sendVerification');
        Route::post('/ndr/{dsOrder}', [SmartShippingController::class, 'handleNdr'])->name('handleNdr');
    });

    // 📢 WhatsApp Marketing & Abandoned Cart Recovery Routes
    Route::prefix('marketing')->name('marketing.')->group(function () {
        Route::get('/abandoned-carts', [MarketingController::class, 'abandonedCarts'])->name('abandonedCarts');
        Route::get('/abandoned-carts/{cart}/send-recovery', [MarketingController::class, 'sendRecovery'])->name('sendRecovery');
        Route::get('/broadcasts', [MarketingController::class, 'broadcasts'])->name('broadcasts');
        Route::post('/broadcasts', [MarketingController::class, 'storeBroadcast'])->name('storeBroadcast');
    });

    // 🧾 GST Tax Invoices & B2B Tiered Slab Pricing
    Route::get('/orders/{order}/tax-invoice', [GstInvoiceController::class, 'show'])->name('orders.taxInvoice');
    Route::get('/products/{product}/pricing-tiers', [GstInvoiceController::class, 'pricingTiers'])->name('products.pricing_tiers');
    Route::post('/products/{product}/pricing-tiers', [GstInvoiceController::class, 'storeTier'])->name('products.store_tier');

    // 📑 PDF & Excel Catalog AI Ingestion Routes
    Route::prefix('catalog')->name('catalog.')->group(function () {
        Route::get('/upload', [CatalogIngestionController::class, 'uploadForm'])->name('upload');
        Route::post('/upload', [CatalogIngestionController::class, 'upload'])->name('store');
        Route::post('/load-plasto-master', [CatalogIngestionController::class, 'loadPlastoMaster'])->name('load_plasto');
        Route::get('/review/{job}', [CatalogIngestionController::class, 'review'])->name('review');
        Route::post('/publish/{job}', [CatalogIngestionController::class, 'publish'])->name('publish');
        Route::get('/export-excel/{job}', [CatalogIngestionController::class, 'exportExcel'])->name('export_excel');
        Route::post('/crop-image', [CatalogIngestionController::class, 'cropImage'])->name('crop_image');

        // 📸 Page 1: Bulk Media Vault & Gallery (Store, Preview, Upload & Delete Images)
        Route::get('/gallery', [CatalogIngestionController::class, 'gallery'])->name('gallery');
        Route::get('/gallery-json', [CatalogIngestionController::class, 'galleryJson'])->name('gallery_json');
        Route::post('/gallery/upload', [CatalogIngestionController::class, 'uploadToGallery'])->name('gallery.upload');
        Route::post('/gallery/assign-folder', [CatalogIngestionController::class, 'assignFolder'])->name('gallery.assign_folder');
        Route::post('/gallery/update-details', [CatalogIngestionController::class, 'updateMediaDetails'])->name('gallery.update_details');
        Route::post('/gallery/delete', [CatalogIngestionController::class, 'deleteFromGallery'])->name('gallery.delete');
        Route::post('/gallery/bulk-delete', [CatalogIngestionController::class, 'bulkDeleteFromGallery'])->name('gallery.bulk_delete');

        // 📄 Page 2: Interactive PDF Studio (Side-by-Side Editable Canvas + Live Gallery)
        Route::get('/pdf-studio', [CatalogIngestionController::class, 'pdfStudio'])->name('pdf_studio');
        Route::post('/pdf-studio/crop-to-gallery', [CatalogIngestionController::class, 'savePdfCropToGallery'])->name('pdf_studio.crop');
        Route::post('/ai-copilot', [CatalogIngestionController::class, 'aiCopilotChat'])->name('ai_copilot');
        Route::post('/pdf-studio/ai-extract-table', [CatalogIngestionController::class, 'aiExtractTableFromPage'])->name('pdf_studio.ai_extract_table');

        // 📊 Page 3: Excel Multi-Row Mapper (6-8 Line Batch Image Assigner & Publisher)
        Route::get('/excel-mapper/{job?}', [CatalogIngestionController::class, 'excelMapper'])->name('excel_mapper');
        Route::post('/excel-mapper/assign-batch', [CatalogIngestionController::class, 'assignBatchImage'])->name('excel_mapper.assign_batch');
        Route::post('/excel-mapper/create-sheet', [CatalogIngestionController::class, 'createSheetFromRows'])->name('excel_mapper.create_sheet');
        Route::post('/excel-mapper/publish-direct', [CatalogIngestionController::class, 'publishDirectFromMapper'])->name('excel_mapper.publish_direct');
        Route::delete('/jobs/{job}', [CatalogIngestionController::class, 'deleteJob'])->name('job.delete');
    });

    // 📦 Inventory & 1-Click Restock Manager
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [CatalogIngestionController::class, 'inventory'])->name('index');
        Route::post('/restock', [CatalogIngestionController::class, 'restock'])->name('restock');
        Route::post('/quick-update-rates', [CatalogIngestionController::class, 'quickUpdateRates'])->name('quick_update_rates');
        Route::post('/bulk-rate-update', [CatalogIngestionController::class, 'bulkRateUpdate'])->name('bulk_rate_update');
        Route::get('/export-price-template', [CatalogIngestionController::class, 'exportPriceTemplate'])->name('export_price_template');
    });

    // 📸 Bulk Photo Studio & Quick Listing Canvas Routes
    Route::prefix('studio')->name('studio.')->group(function () {
        Route::get('/', [PhotoStudioController::class, 'index'])->name('index');
        Route::post('/upload', [PhotoStudioController::class, 'uploadMedia'])->name('upload');
        Route::post('/publish', [PhotoStudioController::class, 'publishProduct'])->name('publish');
        Route::post('/ai-assist', [PhotoStudioController::class, 'aiAssist'])->name('aiAssist');
    });

    // 🏷️ Central Brand Master Catalog & Less Calculator Routes
    Route::prefix('brand-master')->name('brand-master.')->group(function () {
        Route::get('/', [BrandMasterController::class, 'index'])->name('index');
        Route::post('/import', [BrandMasterController::class, 'import'])->name('import');
    });
});