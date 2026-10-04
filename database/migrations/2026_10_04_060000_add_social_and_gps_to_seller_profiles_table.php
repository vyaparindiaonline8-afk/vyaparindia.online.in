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
            if (!Schema::hasColumn('seller_profiles', 'office_phone')) {
                $table->string('office_phone', 30)->nullable();
            }
            if (!Schema::hasColumn('seller_profiles', 'google_map_url')) {
                $table->text('google_map_url')->nullable();
            }
            if (!Schema::hasColumn('seller_profiles', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable();
            }
            if (!Schema::hasColumn('seller_profiles', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable();
            }
            if (!Schema::hasColumn('seller_profiles', 'facebook_url')) {
                $table->text('facebook_url')->nullable();
            }
            if (!Schema::hasColumn('seller_profiles', 'instagram_url')) {
                $table->text('instagram_url')->nullable();
            }
            if (!Schema::hasColumn('seller_profiles', 'youtube_url')) {
                $table->text('youtube_url')->nullable();
            }
            if (!Schema::hasColumn('seller_profiles', 'google_business_url')) {
                $table->text('google_business_url')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'office_phone',
                'google_map_url',
                'latitude',
                'longitude',
                'facebook_url',
                'instagram_url',
                'youtube_url',
                'google_business_url',
            ]);
        });
    }
};
