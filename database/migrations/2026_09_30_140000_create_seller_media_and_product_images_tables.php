<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Seller Batch Photo Studio Media Gallery
        Schema::create('seller_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('filename');
            $table->string('file_path');
            $table->boolean('is_assigned')->default(false);
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->timestamps();
        });

        // 2. Multi-image support for products
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('image_path');
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. Multi-category pivot for products
        if (!Schema::hasTable('category_product')) {
            Schema::create('category_product', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('category_product');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('seller_media');
    }
};
