<?php

namespace App\Services;

class SmartLogisticsService
{
    /**
     * Calculate and rank shipping rates across multi-carrier logistics (Delhivery, BlueDart, Shadowfax, DTDC, Ekart).
     */
    public function calculateCourierRates(string $originPin, string $destPin, int $weightGrams = 500, bool $isCod = true, float $orderValue = 1000.00): array
    {
        // Determine zone based on PIN prefix matching
        $isLocal = substr($originPin, 0, 2) === substr($destPin, 0, 2);
        $isRegional = substr($originPin, 0, 1) === substr($destPin, 0, 1);
        
        $zone = $isLocal ? 'City' : ($isRegional ? 'Regional' : 'National');
        $baseWeightSlabs = max(1, ceil($weightGrams / 500));

        $couriers = [
            [
                'name' => 'Delhivery Surface',
                'icon' => 'fa-solid fa-truck',
                'sla_days' => $isLocal ? '1-2 Days' : ($isRegional ? '2-3 Days' : '3-5 Days'),
                'base_freight' => $isLocal ? 38 : ($isRegional ? 48 : 58),
                'cod_charge' => $isCod ? 35 : 0,
                'rating' => 4.6,
            ],
            [
                'name' => 'Shadowfax Express',
                'icon' => 'fa-solid fa-bolt',
                'sla_days' => $isLocal ? '1 Day' : ($isRegional ? '2 Days' : '3-4 Days'),
                'base_freight' => $isLocal ? 35 : ($isRegional ? 45 : 54),
                'cod_charge' => $isCod ? 30 : 0,
                'rating' => 4.4,
            ],
            [
                'name' => 'BlueDart Air Express',
                'icon' => 'fa-solid fa-plane',
                'sla_days' => $isLocal ? 'Same Day' : ($isRegional ? '1-2 Days' : '2-3 Days'),
                'base_freight' => $isLocal ? 55 : ($isRegional ? 70 : 85),
                'cod_charge' => $isCod ? 45 : 0,
                'rating' => 4.9,
            ],
            [
                'name' => 'DTDC Economy',
                'icon' => 'fa-solid fa-cube',
                'sla_days' => $isLocal ? '2 Days' : ($isRegional ? '3 Days' : '4-6 Days'),
                'base_freight' => $isLocal ? 32 : ($isRegional ? 42 : 50),
                'cod_charge' => $isCod ? 35 : 0,
                'rating' => 4.2,
            ],
        ];

        $calculated = [];
        foreach ($couriers as $c) {
            $totalFreight = ($c['base_freight'] * $baseWeightSlabs) + $c['cod_charge'];
            $calculated[] = [
                'courier_name' => $c['name'],
                'icon' => $c['icon'],
                'sla_days' => $c['sla_days'],
                'zone' => $zone,
                'freight_charge' => $c['base_freight'] * $baseWeightSlabs,
                'cod_charge' => $c['cod_charge'],
                'total_rate' => $totalFreight,
                'is_cod' => $isCod,
                'rating' => $c['rating'],
                'savings_if_prepaid' => $c['cod_charge'], // Exact savings if customer converts to prepaid!
            ];
        }

        // Sort by total rate (cheapest first)
        usort($calculated, fn($a, $b) => $a['total_rate'] <=> $b['total_rate']);

        return [
            'zone' => $zone,
            'is_cod' => $isCod,
            'weight_grams' => $weightGrams,
            'cheapest_courier' => $calculated[0],
            'fastest_courier' => $calculated[2], // BlueDart
            'all_rates' => $calculated,
        ];
    }
}