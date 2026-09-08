<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Connected Channels (Shopify, WooCommerce, Amazon, Flipkart, Meesho)
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('channel_name'); // shopify, woocommerce, amazon, flipkart, meesho
            $table->string('store_name')->nullable();
            $table->string('store_url')->nullable();
            $table->string('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->text('access_token')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_sync_inventory')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });

        // 2. Channel Listings (Map internal products to marketplace IDs)
        Schema::create('channel_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained('channels')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('external_product_id')->nullable();
            $table->string('sync_status')->default('synced'); // pending, synced, failed
            $table->string('listing_url')->nullable();
            $table->decimal('channel_price', 10, 2)->nullable();
            $table->integer('synced_stock')->default(0);
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });

        // Add inventory stock count to products table if not present
        if (!Schema::hasColumn('products', 'stock_quantity')) {
            Schema::table('products', function (Blueprint $table) {
                $table->integer('stock_quantity')->default(50)->after('price');
                $table->string('sku')->nullable()->after('stock_quantity');
                $table->string('hsn_code')->nullable()->after('sku');
                $table->text('tags')->nullable()->after('description');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('channel_listings');
        Schema::dropIfExists('channels');
        if (Schema::hasColumn('products', 'stock_quantity')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn(['stock_quantity', 'sku', 'hsn_code', 'tags']);
            });
        }
    }
};