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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'trade_discount_percent')) {
                $table->decimal('trade_discount_percent', 5, 2)->nullable()->default(0.00)->after('mrp');
            }
            if (!Schema::hasColumn('products', 'net_landing_cost')) {
                $table->decimal('net_landing_cost', 12, 2)->nullable()->default(0.00)->after('purchase_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'trade_discount_percent')) {
                $table->dropColumn('trade_discount_percent');
            }
            if (Schema::hasColumn('products', 'net_landing_cost')) {
                $table->dropColumn('net_landing_cost');
            }
        });
    }
};
