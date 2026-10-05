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
        Schema::table('seller_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_profiles', 'whatsapp_number')) {
                $table->string('whatsapp_number', 25)->nullable()->after('phone_number');
            }
            if (!Schema::hasColumn('seller_profiles', 'support_email')) {
                $table->string('support_email', 191)->nullable()->after('office_phone');
            }
            if (!Schema::hasColumn('seller_profiles', 'background_image')) {
                $table->string('background_image', 500)->nullable()->after('support_email');
            }
        });

        Schema::table('seller_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_pages', 'visits_count')) {
                $table->unsignedBigInteger('visits_count')->default(0)->after('business_type');
            }
            if (!Schema::hasColumn('seller_pages', 'show_last_updated_to_buyers')) {
                $table->boolean('show_last_updated_to_buyers')->default(false)->after('visits_count');
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
        Schema::table('seller_profiles', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_number', 'support_email', 'background_image']);
        });

        Schema::table('seller_pages', function (Blueprint $table) {
            $table->dropColumn(['visits_count', 'show_last_updated_to_buyers']);
        });
    }
};
