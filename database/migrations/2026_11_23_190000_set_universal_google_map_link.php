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
        $universalMap = 'https://www.google.com/maps/search/?api=1&query=' . urlencode('Ganesh Traders, Main Road, Near Bus Stand, Sarangarh, Chhattisgarh 496445');
        DB::table('seller_pages')
            ->where('slug', 'ganesh-tradersh')
            ->update([
                'google_map_link' => $universalMap,
            ]);

        DB::table('seller_profiles')
            ->where('user_id', 8)
            ->update([
                'google_map_url' => $universalMap,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
