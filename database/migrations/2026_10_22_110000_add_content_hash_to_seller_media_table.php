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
            if (!Schema::hasColumn('seller_media', 'content_hash')) {
                $table->string('content_hash', 64)->nullable()->index();
            }
            if (!Schema::hasColumn('seller_media', 'file_size')) {
                $table->unsignedBigInteger('file_size')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_media', function (Blueprint $table) {
            if (Schema::hasColumn('seller_media', 'content_hash')) {
                $table->dropColumn('content_hash');
            }
            if (Schema::hasColumn('seller_media', 'file_size')) {
                $table->dropColumn('file_size');
            }
        });
    }
};
