<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingRate;

class ShippingRateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            // Amazon Easy Ship Slabs
            ['carrier' => 'amazon', 'weight' => 0.5, 'rate' => 55.00, 'gst_percent' => 18.00, 'label' => '0.5 kg (Amazon Easy Ship ₹55)'],
            ['carrier' => 'amazon', 'weight' => 1.0, 'rate' => 75.00, 'gst_percent' => 18.00, 'label' => '1.0 kg (Amazon Easy Ship ₹75)'],
            ['carrier' => 'amazon', 'weight' => 2.0, 'rate' => 112.00, 'gst_percent' => 18.00, 'label' => '2.0 kg (Amazon Easy Ship ₹112)'],
            ['carrier' => 'amazon', 'weight' => 3.0, 'rate' => 145.00, 'gst_percent' => 18.00, 'label' => '3.0 kg (Amazon Easy Ship ₹145)'],

            // Meesho Logistics Slabs
            ['carrier' => 'meesho', 'weight' => 0.5, 'rate' => 38.00, 'gst_percent' => 18.00, 'label' => '0.5 kg (Meesho Logistics ₹38)'],
            ['carrier' => 'meesho', 'weight' => 1.0, 'rate' => 52.00, 'gst_percent' => 18.00, 'label' => '1.0 kg (Meesho Logistics ₹52)'],
            ['carrier' => 'meesho', 'weight' => 2.0, 'rate' => 75.00, 'gst_percent' => 18.00, 'label' => '2.0 kg (Meesho Logistics ₹75)'],
            ['carrier' => 'meesho', 'weight' => 3.0, 'rate' => 95.00, 'gst_percent' => 18.00, 'label' => '3.0 kg (Meesho Logistics ₹95)'],

            // Flipkart Ekart Slabs
            ['carrier' => 'flipkart', 'weight' => 0.5, 'rate' => 48.00, 'gst_percent' => 18.00, 'label' => '0.5 kg (Flipkart Ekart ₹48)'],
            ['carrier' => 'flipkart', 'weight' => 1.0, 'rate' => 68.00, 'gst_percent' => 18.00, 'label' => '1.0 kg (Flipkart Ekart ₹68)'],
            ['carrier' => 'flipkart', 'weight' => 2.0, 'rate' => 98.00, 'gst_percent' => 18.00, 'label' => '2.0 kg (Flipkart Ekart ₹98)'],
            ['carrier' => 'flipkart', 'weight' => 3.0, 'rate' => 125.00, 'gst_percent' => 18.00, 'label' => '3.0 kg (Flipkart Ekart ₹125)'],

            // Direct / Self Ship Slabs
            ['carrier' => 'direct', 'weight' => 0.5, 'rate' => 45.00, 'gst_percent' => 18.00, 'label' => '0.5 kg (Self / Shiprocket ₹45)'],
            ['carrier' => 'direct', 'weight' => 1.0, 'rate' => 70.00, 'gst_percent' => 18.00, 'label' => '1.0 kg (Self / Shiprocket ₹70)'],
            ['carrier' => 'direct', 'weight' => 2.0, 'rate' => 105.00, 'gst_percent' => 18.00, 'label' => '2.0 kg (Self / Shiprocket ₹105)'],
            ['carrier' => 'direct', 'weight' => 3.0, 'rate' => 140.00, 'gst_percent' => 18.00, 'label' => '3.0 kg (Self / Shiprocket ₹140)'],
        ];

        foreach ($rates as $r) {
            ShippingRate::updateOrCreate(
                ['carrier' => $r['carrier'], 'weight' => $r['weight']],
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
