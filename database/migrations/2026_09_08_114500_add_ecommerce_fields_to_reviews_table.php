<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->unsignedBigInteger('seller_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('order_id')->nullable()->after('seller_id');
            $table->string('customer_name')->nullable()->after('order_id');
            $table->string('customer_city')->nullable()->after('customer_name');
            $table->string('review_title')->nullable()->after('customer_city');
            $table->boolean('is_verified_purchase')->default(true)->after('comment');
            $table->boolean('is_approved')->default(true)->after('is_verified_purchase');
            $table->integer('helpful_votes')->default(0)->after('is_approved');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn([
                'seller_id', 'order_id', 'customer_name', 'customer_city',
                'review_title', 'is_verified_purchase', 'is_approved', 'helpful_votes'
            ]);
        });
    }
};