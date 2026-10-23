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
Route::match(['GET', 'POST', 'HEAD'], '/index.php', [HomeController::class, 'index']);
Route::get('/favicon.ico', fn() => response('', 204));
Route::get('/health', fn() => response('OK', 200)->header('Content-Type', 'text/plain'));
Route::get('/up', fn() => response('OK', 200)->header('Content-Type', 'text/plain'));
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/product/{product:slug}', [HomeController::class, 'showProduct'])->name('product.show');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/search-sellers-by-city', [HomeController::class, 'searchSellersByCity'])->name('search_sellers_by_city');
Route::view('/how-it-works/algorithm', 'public.algorithm')->name('marketplace.algorithm');

// Static Information Pages
Route::view('/about', 'public.about')->name('about');
Route::view('/help', 'public.help')->name('help');
Route::view('/privacy-policy', 'public.privacy')->name('privacy');
Route::view('/terms', 'public.terms')->name('terms');

// Authenticated Wishlist Routes
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [\App\Http\Controllers\Buyer\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{product}', [\App\Http\Controllers\Buyer\WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{product}', [\App\Http\Controllers\Buyer\WishlistController::class, 'destroy'])->name('wishlist.destroy');
});

// Public WhatsApp Order Verification & Instant Prepaid Conversion Routes
Route::prefix('order-verify')->name('verification.')->group(function () {
    Route::get('/{token}', [\App\Http\Controllers\OrderVerificationController::class, 'show'])->name('show');
    Route::post('/{token}/pay', [\App\Http\Controllers\OrderVerificationController::class, 'payPrepaid'])->name('pay');
    Route::post('/{token}/confirm-cod', [\App\Http\Controllers\OrderVerificationController::class, 'confirmCod'])->name('confirmCod');
});

// 🤖 AI Copilot & Voice Calling Module (Interactive Store Assistant & Auto-Listing)
Route::prefix('ai-copilot')->name('copilot.')->group(function () {
    Route::post('/chat', [\App\Http\Controllers\AICopilotController::class, 'chat'])->name('chat');
    Route::post('/publish-draft', [\App\Http\Controllers\AICopilotController::class, 'publishDraft'])->name('publish_draft');
    Route::post('/update-price', [\App\Http\Controllers\AICopilotController::class, 'updatePrice'])->name('update_price');
    Route::match(['GET', 'POST'], '/voice-webhook', [\App\Http\Controllers\AICopilotController::class, 'voiceWebhook'])->name('voice_webhook');
});

// 🌐 Multi-Channel Real-Time Inbound Webhooks (Amazon, Meesho, Flipkart, Shopify, WooCommerce)
Route::match(['GET', 'POST'], '/api/webhooks/channel/{channel}', [\App\Http\Controllers\Seller\ChannelWebhookController::class, 'handleWebhook'])->name('channel.webhook');
Route::match(['GET', 'POST'], '/webhooks/channel/{channel}', [\App\Http\Controllers\Seller\ChannelWebhookController::class, 'handleWebhook']);

// Auth routes are in routes/auth.php
// Buyer routes are in routes/buyer.php
// Seller routes are in routes/seller.php
// Admin routes are in routes/admin.php

require __DIR__.'/auth.php';
require __DIR__.'/buyer.php';
require __DIR__.'/seller.php';
require __DIR__.'/admin.php';
require __DIR__.'/minisite.php';

