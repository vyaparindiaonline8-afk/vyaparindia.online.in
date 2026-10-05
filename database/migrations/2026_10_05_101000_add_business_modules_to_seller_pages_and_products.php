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
        Schema::table('seller_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_pages', 'business_type')) {
                $table->string('business_type', 50)->default('hardware_pipes')->after('theme_style')->index();
            }
            if (!Schema::hasColumn('seller_pages', 'business_settings')) {
                $table->json('business_settings')->nullable()->after('business_type');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'attributes')) {
                $table->json('attributes')->nullable()->after('tags');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seller_pages', function (Blueprint $table) {
            if (Schema::hasColumn('seller_pages', 'business_type')) {
                $table->dropColumn('business_type');
            }
            if (Schema::hasColumn('seller_pages', 'business_settings')) {
                $table->dropColumn('business_settings');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'attributes')) {
                $table->dropColumn('attributes');
            }
        });
    }
};
