<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('seller_expenses')) {
            Schema::create('seller_expenses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('expense_type')->default('operational'); // operational, fixed, overhead, marketing
                $table->string('category'); // rent, salary, electricity, cartage, packing, ads, ca_audit, software, misc
                $table->decimal('amount', 12, 2);
                $table->date('expense_date');
                $table->string('platform')->default('general'); // general, amazon, meesho, flipkart, vyaparindia, dropship
                $table->string('payment_mode')->default('bank_upi'); // cash, bank_upi, cheque, card
                $table->string('paid_to')->nullable(); // landlord, staff name, courier, etc.
                $table->string('invoice_receipt_no')->nullable();
                $table->text('notes')->nullable();
                $table->boolean('is_recurring')->default(false); // monthly recurring flag
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('seller_other_incomes')) {
            Schema::create('seller_other_incomes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('income_type')->default('other'); // scrap, refund, supplier_discount, interest, reward
                $table->decimal('amount', 12, 2);
                $table->date('income_date');
                $table->string('platform')->default('general');
                $table->string('received_from')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_expenses');
        Schema::dropIfExists('seller_other_incomes');
    }
};
