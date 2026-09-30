<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Catalog Ingestion Jobs
        Schema::create('catalog_ingestion_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('filename');
            $table->string('file_path');
            $table->string('status')->default('pending'); // pending, processing, ready_for_review, published, failed
            $table->integer('total_products_detected')->default(0);
            $table->longText('extracted_data')->nullable(); // JSON structure of extracted items
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        // 2. Product Variants (Multi-range, e.g. CPVC pipe sizes/grades)
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('variant_name'); // e.g. 1/2" (15mm) SDR 11
            $table->string('sku')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->decimal('trade_discount_percent', 5, 2)->default(0.00);
            $table->decimal('gst_percent', 5, 2)->default(18.00);
            $table->decimal('net_landing_cost', 10, 2)->nullable();
            $table->decimal('wholesale_price', 10, 2)->nullable();
            $table->decimal('retail_price', 10, 2)->default(0.00);
            $table->decimal('mrp', 10, 2)->nullable();
            $table->integer('stock_quantity')->nullable(); // OPTIONAL: dale to thik, na dale to unlimited
            $table->boolean('track_inventory')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('image_url')->nullable();
            $table->json('attributes')->nullable(); // e.g. {"size": "1/2 inch", "grade": "SDR 11"}
            $table->timestamps();
        });

        // 3. Stock Movements (Inventory Audit Ledger)
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // in_restock, out_order, in_cancellation, out_adjustment
            $table->integer('quantity'); // positive for addition, negative for deduction
            $table->integer('balance_after');
            $table->string('reason')->nullable();
            $table->string('reference_type')->nullable(); // order, manual_restock, pdf_import
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();
        });

        // 4. Update products table to flag variable products & optional stock tracking
        if (!Schema::hasColumn('products', 'has_variants')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('has_variants')->default(false)->after('price');
                $table->boolean('track_inventory')->default(false)->after('stock_quantity');
                $table->decimal('purchase_price', 10, 2)->nullable()->after('price');
                $table->decimal('wholesale_price', 10, 2)->nullable()->after('purchase_price');
                $table->decimal('mrp', 10, 2)->nullable()->after('wholesale_price');
                $table->decimal('gst_percent', 5, 2)->default(18.00)->after('mrp');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'has_variants')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn(['has_variants', 'track_inventory', 'purchase_price', 'wholesale_price', 'mrp', 'gst_percent']);
            });
        }

        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('catalog_ingestion_jobs');
    }
};