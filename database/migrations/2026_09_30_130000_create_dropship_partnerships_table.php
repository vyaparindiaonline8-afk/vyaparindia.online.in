<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dropship_partnerships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('retailer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('wholesaler_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('request_note')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['retailer_id', 'wholesaler_id']);
        });

        // Add tier column to users if not present
        if (!Schema::hasColumn('users', 'business_tier')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('business_tier')->default('profile_only'); // profile_only, minisite, dropship
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('dropship_partnerships');
        if (Schema::hasColumn('users', 'business_tier')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('business_tier');
            });
        }
    }
};
