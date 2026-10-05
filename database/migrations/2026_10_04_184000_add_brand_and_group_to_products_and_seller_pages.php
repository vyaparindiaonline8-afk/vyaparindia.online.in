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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'brand')) {
                $table->string('brand')->nullable()->after('name')->index();
            }
            if (!Schema::hasColumn('products', 'group_name')) {
                $table->string('group_name')->nullable()->after('brand')->index();
            }
        });

        Schema::table('seller_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_pages', 'authorized_brands')) {
                $table->json('authorized_brands')->nullable()->after('theme_style');
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
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'brand')) {
                $table->dropColumn('brand');
            }
            if (Schema::hasColumn('products', 'group_name')) {
                $table->dropColumn('group_name');
            }
        });

        Schema::table('seller_pages', function (Blueprint $table) {
            if (Schema::hasColumn('seller_pages', 'authorized_brands')) {
                $table->dropColumn('authorized_brands');
            }
        });
    }
};
