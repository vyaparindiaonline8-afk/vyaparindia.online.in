<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_page_id')->constrained('seller_pages')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('domain')->unique();
            $table->string('cname_target')->default('cname.vyaparindia.online');
            $table->string('verification_txt')->unique();
            $table->string('dns_status')->default('pending'); // pending, verified, failed
            $table->string('ssl_status')->default('pending'); // pending, provisioning, active
            $table->boolean('is_primary')->default(true);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_domains');
    }
};