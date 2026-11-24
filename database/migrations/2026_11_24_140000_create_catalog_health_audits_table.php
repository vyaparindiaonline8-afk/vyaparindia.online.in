<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('catalog_health_audits')) {
            Schema::create('catalog_health_audits', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('seller_page_id')->nullable()->index();
                $table->integer('health_score')->default(0); // 0 to 100
                $table->json('breakdown')->nullable(); // scores for images, stock, pricing, content
                $table->json('stats')->nullable(); // counts for in_stock, out_of_stock, missing_images, etc.
                $table->json('recommendations')->nullable(); // actionable steps to reach 100
                $table->json('products_audit')->nullable(); // detailed list of products with stock, price, images, score
                $table->timestamp('audited_at')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catalog_health_audits');
    }
};
