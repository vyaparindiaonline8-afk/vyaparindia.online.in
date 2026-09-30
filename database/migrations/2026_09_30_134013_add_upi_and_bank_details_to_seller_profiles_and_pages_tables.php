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
        Schema::table('seller_profiles', function (Blueprint $table) {
            $table->string('upi_id')->nullable()->after('dispatch_radius');
            $table->string('bank_name')->nullable()->after('upi_id');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_ifsc')->nullable()->after('bank_account_number');
            $table->string('bank_account_holder')->nullable()->after('bank_ifsc');
        });

        Schema::table('seller_pages', function (Blueprint $table) {
            $table->string('upi_id')->nullable()->after('enable_whatsapp_order');
            $table->string('bank_name')->nullable()->after('upi_id');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_ifsc')->nullable()->after('bank_account_number');
            $table->string('bank_account_holder')->nullable()->after('bank_ifsc');
            $table->boolean('show_payment_details_to_buyer')->default(true)->after('bank_account_holder');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            $table->dropColumn(['upi_id', 'bank_name', 'bank_account_number', 'bank_ifsc', 'bank_account_holder']);
        });

        Schema::table('seller_pages', function (Blueprint $table) {
            $table->dropColumn(['upi_id', 'bank_name', 'bank_account_number', 'bank_ifsc', 'bank_account_holder', 'show_payment_details_to_buyer']);
        });
    }
};
