<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->unsignedBigInteger('seller_id')->nullable()->after('user_id');
            $table->string('order_number')->nullable()->unique()->after('id');
            $table->string('order_source')->default('minisite')->after('order_number');
            $table->string('customer_name')->nullable()->after('total_price');
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->string('customer_email')->nullable()->after('customer_phone');
            $table->text('shipping_address')->nullable()->after('customer_email');
            $table->string('city')->nullable()->after('shipping_address');
            $table->string('state')->nullable()->after('city');
            $table->string('pincode')->nullable()->after('state');
            $table->string('payment_method')->default('cod')->after('pincode');
            $table->string('payment_status')->default('pending')->after('payment_method');
            $table->text('notes')->nullable()->after('status');

            $table->foreign('seller_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['seller_id']);
            $table->dropColumn([
                'seller_id', 'order_number', 'order_source', 'customer_name',
                'customer_phone', 'customer_email', 'shipping_address', 'city',
                'state', 'pincode', 'payment_method', 'payment_status', 'notes'
            ]);
        });
    }
};
