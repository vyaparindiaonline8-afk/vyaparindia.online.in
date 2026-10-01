<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_pages', function (Blueprint $table) {
            $table->string('google_review_link')->nullable()->after('facebook_link');
            $table->string('google_map_link')->nullable()->after('google_review_link');
            $table->string('youtube_link')->nullable()->after('google_map_link');
        });
    }

    public function down(): void
    {
        Schema::table('seller_pages', function (Blueprint $table) {
            $table->dropColumn(['google_review_link', 'google_map_link', 'youtube_link']);
        });
    }
};
