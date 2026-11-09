<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingRate;

class ShippingRateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            ['weight' => 0.5, 'rate' => 55.00, 'gst_percent' => 18.00, 'label' => '0.5 kg (₹55 + 18% GST)'],
            ['weight' => 1.0, 'rate' => 75.00, 'gst_percent' => 18.00, 'label' => '1.0 kg (₹75 + 18% GST)'],
            ['weight' => 2.0, 'rate' => 112.00, 'gst_percent' => 18.00, 'label' => '2.0 kg (₹112 + 18% GST)'],
        ];

        foreach ($rates as $r) {
            ShippingRate::updateOrCreate(
                ['carrier' => 'amazon_easy_ship', 'weight' => $r['weight']],
                [
                    'zone' => 'standard',
                    'rate' => $r['rate'],
                    'gst_percent' => $r['gst_percent'],
                    'label' => $r['label'],
                    'is_active' => true,
                ]
            );
        }
    }
}
