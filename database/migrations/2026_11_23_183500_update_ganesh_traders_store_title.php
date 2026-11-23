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
        // 1. Update SellerPage: Set exact store name to Ganesh Traders
        DB::table('seller_pages')
            ->where('slug', 'ganesh-tradersh')
            ->update([
                'page_title' => 'Ganesh Traders',
                'dealership_text' => 'Authorized Plasto Tanks & Plumbing Solutions Distributor',
                'tagline' => 'Plasto Tanks, UPVC, CPVC & SWR Plumbing Solutions at Factory Rates',
            ]);

        // 2. Update Users & SellerProfiles for owner Ankush
        DB::table('users')
            ->where('email', 'ankush.bani@gmail.com')
            ->update([
                'business_name' => 'Ganesh Traders',
            ]);

        DB::table('seller_profiles')
            ->where('user_id', 8)
            ->update([
                'business_name' => 'Ganesh Traders',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
