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
        Schema::table('seller_pages', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('page_title');
            $table->string('banner_image')->nullable()->after('logo');
            $table->string('tagline')->nullable()->after('banner_image');
            $table->text('about_text')->nullable()->after('welcome_message');
            $table->string('whatsapp_number')->nullable()->after('about_text');
            $table->string('support_phone')->nullable()->after('whatsapp_number');
            $table->string('support_email')->nullable()->after('support_phone');
            $table->string('instagram_link')->nullable()->after('support_email');
            $table->string('facebook_link')->nullable()->after('instagram_link');
            $table->text('address')->nullable()->after('facebook_link');
            $table->string('city')->nullable()->after('address');
            $table->string('pincode')->nullable()->after('city');
            $table->string('theme_style')->default('modern')->after('theme_color'); // modern, minimal, vibrant
            $table->string('currency')->default('INR')->after('theme_style');
            $table->boolean('enable_cod')->default(true)->after('currency');
            $table->boolean('enable_whatsapp_order')->default(true)->after('enable_cod');
            $table->text('policies')->nullable()->after('enable_whatsapp_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_pages', function (Blueprint $table) {
            $table->dropColumn([
                'logo', 'banner_image', 'tagline', 'about_text',
                'whatsapp_number', 'support_phone', 'support_email',
                'instagram_link', 'facebook_link', 'address', 'city', 'pincode',
                'theme_style', 'currency', 'enable_cod', 'enable_whatsapp_order', 'policies'
            ]);
        });
    }
};
