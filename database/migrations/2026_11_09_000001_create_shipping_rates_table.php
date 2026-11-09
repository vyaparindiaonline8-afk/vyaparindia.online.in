<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('shipping_rates')) {
            Schema::create('shipping_rates', function (Blueprint $table) {
                $table->id();
                $table->string('carrier')->default('amazon_easy_ship');
                $table->string('zone')->default('standard');
                $table->decimal('weight', 8, 2)->comment('Weight in kg');
                $table->decimal('rate', 8, 2)->comment('Courier rate before GST');
                $table->decimal('gst_percent', 5, 2)->default(18.00);
                $table->string('label')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};
