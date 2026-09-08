<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payout_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('payout_type')->default('upi'); // upi, bank_account
            $table->string('upi_id')->nullable();
            $table->string('account_holder_name');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->boolean('is_default')->default(true);
            $table->timestamps();
        });

        Schema::create('payout_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('dropship_wallets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('payout_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('payout_method')->default('upi'); // upi, imps, neft
            $table->json('beneficiary_details');
            $table->string('status')->default('completed'); // pending, processing, completed, failed
            $table->string('utr_number')->nullable();
            $table->string('gateway_reference')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_requests');
        Schema::dropIfExists('payout_accounts');
    }
};