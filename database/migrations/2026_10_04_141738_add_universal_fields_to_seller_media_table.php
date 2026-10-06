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
        Schema::table('seller_media', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_media', 'is_universal')) {
                $table->boolean('is_universal')->default(false);
            }
            if (!Schema::hasColumn('seller_media', 'permission_granted')) {
                $table->boolean('permission_granted')->default(false);
            }
            if (!Schema::hasColumn('seller_media', 'category_type')) {
                $table->string('category_type')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_media', function (Blueprint $table) {
            $table->dropColumn(['is_universal', 'permission_granted', 'category_type']);
        });
    }
};
