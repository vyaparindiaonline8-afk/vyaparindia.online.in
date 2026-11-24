<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('quotations')) {
            Schema::create('quotations', function (Blueprint $table) {
                $table->id();
                $table->string('quotation_number')->unique();
                $table->foreignId('seller_page_id')->nullable()->constrained('seller_pages')->nullOnDelete();
                $table->foreignId('seller_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('customer_name');
                $table->string('customer_phone');
                $table->text('customer_address')->nullable();
                $table->string('project_name')->nullable();
                $table->json('items');
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->decimal('discount_percent', 5, 2)->default(0);
                $table->decimal('discount_amount', 12, 2)->default(0);
                $table->decimal('tax_amount', 12, 2)->default(0);
                $table->decimal('grand_total', 12, 2)->default(0);
                $table->string('status')->default('pending'); // pending, approved, converted_to_order, rejected
                $table->date('valid_until')->nullable();
                $table->text('customer_notes')->nullable();
                $table->text('owner_notes')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->foreignId('converted_order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
