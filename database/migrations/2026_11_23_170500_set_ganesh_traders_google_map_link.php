<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('seller_pages')
            ->where('slug', 'ganesh-tradersh')
            ->update([
                'google_map_link' => 'https://maps.app.goo.gl/N5jLBpTdzTt9w4EB7'
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
