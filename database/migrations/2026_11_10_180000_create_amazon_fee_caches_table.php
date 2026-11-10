<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('amazon_fee_caches')) {
            Schema::create('amazon_fee_caches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('channel_id')->nullable()->constrained('channels')->nullOnDelete();
                $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnDelete();
                $table->string('marketplace')->default('amazon'); // amazon, meesho, flipkart, myntra, blinkit
                $table->string('sku')->nullable()->index();
                $table->string('asin')->nullable()->index();
                $table->decimal('listing_price', 10, 2)->default(0);
                $table->decimal('weight_kg', 8, 3)->default(0.5);
                $table->decimal('referral_fee', 10, 2)->default(0);
                $table->decimal('closing_fee', 10, 2)->default(0);
                $table->decimal('shipping_fee', 10, 2)->default(0);
                $table->decimal('tax_fee', 10, 2)->default(0); // 18% GST on platform fees
                $table->decimal('total_fee', 10, 2)->default(0);
                $table->decimal('effective_fee_pct', 5, 2)->default(0);
                $table->boolean('is_actual')->default(false); // true if fetched from live SP-API, false if deterministic category matrix
                $table->string('source')->default('matrix'); // sp_api, matrix, fallback, user_override
                $table->json('fee_breakdown')->nullable();
                $table->timestamp('fetched_at')->nullable();
                $table->timestamps();

                $table->index(['marketplace', 'sku', 'listing_price']);
                $table->index(['marketplace', 'asin', 'listing_price']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('amazon_fee_caches');
    }
};
