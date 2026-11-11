<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_profiles', 'tax_scheme')) {
                $table->string('tax_scheme')->default('regular')->after('gst_number'); // 'regular', 'composition', 'unregistered'
            }
            if (!Schema::hasColumn('seller_profiles', 'gst_enrolment_id')) {
                $table->string('gst_enrolment_id')->nullable()->after('tax_scheme'); // For Meesho/Non-GST seller enrolment id
            }
        });
    }

    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('seller_profiles', 'tax_scheme')) {
                $table->dropColumn('tax_scheme');
            }
            if (Schema::hasColumn('seller_profiles', 'gst_enrolment_id')) {
                $table->dropColumn('gst_enrolment_id');
            }
        });
    }
};
