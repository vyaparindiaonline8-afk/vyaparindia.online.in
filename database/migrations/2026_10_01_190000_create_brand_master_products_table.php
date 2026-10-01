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
        if (!Schema::hasTable('brand_master_products')) {
            Schema::create('brand_master_products', function (Blueprint $table) {
                $table->id();
                $table->string('brand_name')->index(); // e.g. 'Plasto'
                $table->string('category_name')->index(); // e.g. 'CPVC Fittings', 'CPVC Pipes'
                $table->string('product_name'); // Full display name
                $table->string('item_type')->nullable(); // Elbow, Tee, Coupler, Pipe
                $table->string('size_mm')->nullable(); // 15, 20, 25, 32...
                $table->string('size_inch')->nullable(); // 1/2", 3/4", 1"...
                $table->string('product_code')->nullable(); // 12-7, 15-5...
                $table->decimal('list_price', 10, 2)->default(0); // Company MRP / List Code Price
                $table->integer('box_qty')->nullable(); // Box packing qty
                $table->string('pouch_qty')->nullable(); // Pouch packing qty
                $table->decimal('default_gst_percent', 5, 2)->default(18.00);
                $table->string('hsn_code')->default('39174000');
                $table->string('image_url')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_master_products');
    }
};
