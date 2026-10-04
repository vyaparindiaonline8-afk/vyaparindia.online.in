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
            $table->boolean('is_universal')->default(false)->after('is_assigned');
            $table->boolean('permission_granted')->default(false)->after('is_universal');
            $table->string('category_type')->nullable()->after('permission_granted');
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
