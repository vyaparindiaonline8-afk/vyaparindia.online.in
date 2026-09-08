<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('tier_name'); // e.g., Small Wholesale, Bulk Factory, Master Carton
            $table->integer('min_quantity');
            $table->integer('max_quantity')->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount_percent', 5, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('gst_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->string('invoice_type')->default('b2c'); // b2b, b2c
            
            // Seller GST Details
            $table->string('seller_gstin')->nullable();
            $table->string('seller_legal_name');
            $table->string('seller_state_code', 2)->default('09'); // 09 = UP, 07 = Delhi, 27 = Maharashtra
            $table->string('seller_state_name')->default('Uttar Pradesh');
            
            // Buyer GST Details
            $table->string('buyer_gstin')->nullable();
            $table->string('buyer_legal_name');
            $table->string('buyer_state_code', 2)->default('09');
            $table->string('buyer_state_name')->default('Uttar Pradesh');
            
            // Taxation Breakdown
            $table->boolean('is_interstate')->default(false);
            $table->decimal('taxable_amount', 10, 2);
            $table->decimal('cgst_rate', 5, 2)->default(9.00);
            $table->decimal('cgst_amount', 10, 2)->default(0.00);
            $table->decimal('sgst_rate', 5, 2)->default(9.00);
            $table->decimal('sgst_amount', 10, 2)->default(0.00);
            $table->decimal('igst_rate', 5, 2)->default(0.00);
            $table->decimal('igst_amount', 10, 2)->default(0.00);
            $table->decimal('total_tax', 10, 2);
            $table->decimal('invoice_total', 10, 2);
            $table->date('invoice_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gst_invoices');
        Schema::dropIfExists('product_pricing_tiers');
    }
};