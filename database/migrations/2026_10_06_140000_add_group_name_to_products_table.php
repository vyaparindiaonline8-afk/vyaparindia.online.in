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
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'group_name')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('group_name')->nullable()->after('brand');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'group_name')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('group_name');
            });
        }
    }
};
