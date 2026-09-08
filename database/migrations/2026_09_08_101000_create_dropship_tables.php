<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Dropshipper imported products
        Schema::create('dropship_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dropshipper_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('wholesaler_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('supplier_product_id')->constrained('products')->onDelete('cascade');
            $table->string('custom_name')->nullable();
            $table->decimal('wholesale_price', 10, 2);
            $table->decimal('retail_price', 10, 2);
            $table->decimal('profit_margin', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Dropship fulfillment orders
        Schema::create('dropship_orders', function (Blueprint $table) {
            $table->id();
            $table->string('ds_order_number')->unique();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('dropshipper_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('wholesaler_id')->constrained('users')->onDelete('cascade');
            
            // Financials
            $table->decimal('customer_retail_total', 10, 2);
            $table->decimal('supplier_base_cost', 10, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0.00);
            $table->decimal('total_supplier_payable', 10, 2);
            $table->decimal('dropshipper_profit', 10, 2);
            
            // Wholesaler adjustments (Low volume adjustment / Shipping adjustment)
            $table->boolean('price_adjusted_by_wholesaler')->default(false);
            $table->text('wholesaler_adjustment_note')->nullable();
            $table->string('dropshipper_approval_status')->default('approved'); // pending_approval, approved, rejected
            
            // Fulfillment Lifecycle
            $table->string('fulfillment_status')->default('pending_wholesaler_review'); 
            // pending_wholesaler_review, awaiting_dropshipper_approval, ready_to_pack, dispatched, delivered, cancelled
            
            // Logistics & Tracking
            $table->string('courier_partner')->nullable();
            $table->string('awb_number')->nullable();
            $table->string('tracking_url')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            
            // Payment & COD Reconciliation
            $table->string('payment_collection_mode')->default('cod'); // cod, prepaid
            $table->string('cod_remittance_status')->default('pending'); // pending, collected_by_courier, remitted_to_dropshipper
            
            $table->timestamps();
        });

        // 3. Dropship Wallets
        Schema::create('dropship_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->decimal('balance', 12, 2)->default(0.00);
            $table->decimal('pending_balance', 12, 2)->default(0.00);
            $table->decimal('total_earned', 12, 2)->default(0.00);
            $table->decimal('total_withdrawn', 12, 2)->default(0.00);
            $table->timestamps();
        });

        // 4. Wallet Transactions Ledger
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('dropship_wallets')->onDelete('cascade');
            $table->string('type'); // credit, debit
            $table->decimal('amount', 10, 2);
            $table->string('reference_type')->nullable(); // dropship_order, payout
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('dropship_wallets');
        Schema::dropIfExists('dropship_orders');
        Schema::dropIfExists('dropship_products');
    }
};