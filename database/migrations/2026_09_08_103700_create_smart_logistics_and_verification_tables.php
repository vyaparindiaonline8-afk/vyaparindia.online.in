<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add verification and anti-RTO fields to orders
        Schema::table('orders', function (Blueprint $table) {
            $table->string('cod_verification_status')->default('unverified')->after('payment_status'); 
            // unverified, verified, converted_to_prepaid, rto_risk_timeout, cancelled
            $table->string('cod_verification_token')->nullable()->unique()->after('cod_verification_status');
            $table->timestamp('verification_deadline_at')->nullable()->after('cod_verification_token');
            $table->decimal('cod_to_prepaid_discount', 10, 2)->default(0.00)->after('verification_deadline_at');
            $table->decimal('original_cod_total', 10, 2)->nullable()->after('cod_to_prepaid_discount');
            $table->string('rto_risk_score')->default('low')->after('original_cod_total'); // low, medium, high
        });

        // 2. Add NDR (Non-Delivery Report) fields to dropship_orders
        Schema::table('dropship_orders', function (Blueprint $table) {
            $table->string('ndr_status')->default('none')->after('cod_remittance_status'); 
            // none, ndr_raised, reattempt_requested, rto_initiated, delivered_after_ndr
            $table->text('ndr_reason')->nullable()->after('ndr_status');
            $table->date('ndr_reattempt_date')->nullable()->after('ndr_reason');
            $table->text('ndr_customer_remarks')->nullable()->after('ndr_reattempt_date');
            $table->decimal('estimated_shipping_rate', 10, 2)->nullable()->after('ndr_customer_remarks');
            $table->string('recommended_courier')->nullable()->after('estimated_shipping_rate');
        });
    }

    public function down(): void
    {
        Schema::table('dropship_orders', function (Blueprint $table) {
            $table->dropColumn([
                'ndr_status', 'ndr_reason', 'ndr_reattempt_date',
                'ndr_customer_remarks', 'estimated_shipping_rate', 'recommended_courier'
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'cod_verification_status', 'cod_verification_token',
                'verification_deadline_at', 'cod_to_prepaid_discount',
                'original_cod_total', 'rto_risk_score'
            ]);
        });
    }
};