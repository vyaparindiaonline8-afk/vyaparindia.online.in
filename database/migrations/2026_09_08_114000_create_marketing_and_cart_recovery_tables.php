<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abandoned_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->json('cart_items');
            $table->decimal('total_amount', 10, 2);
            $table->string('recovery_token')->unique();
            $table->decimal('recovery_discount_percent', 5, 2)->default(10.00);
            $table->decimal('recovery_discount_amount', 10, 2)->default(0.00);
            $table->string('recovery_status')->default('pending'); // pending, whatsapp_sent, recovered, expired
            $table->timestamp('recovery_sent_at')->nullable();
            $table->timestamp('recovered_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('broadcast_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->string('campaign_name');
            $table->text('message_template');
            $table->string('target_audience')->default('all_customers'); // all_customers, repeat_buyers, high_value
            $table->integer('recipient_count')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->integer('order_count')->default(0);
            $table->string('status')->default('sent'); // draft, sent, scheduled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_campaigns');
        Schema::dropIfExists('abandoned_carts');
    }
};