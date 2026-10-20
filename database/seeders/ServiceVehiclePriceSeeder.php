<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceVehiclePrice;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class ServiceVehiclePriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fullService = Service::where('code', 'SRV-001')->first();
        if (! $fullService) {
            return;
        }

        $rates = [
            'Car' => ['price' => 3500.00, 'cost' => 1000.00],
            'Van' => ['price' => 5000.00, 'cost' => 1500.00],
            'SUV' => ['price' => 6200.00, 'cost' => 2000.00],
            'Motorbike' => ['price' => 1200.00, 'cost' => 400.00],
            'Lorry' => ['price' => 8000.00, 'cost' => 3000.00],
        ];

        foreach ($rates as $vehicleTypeName => $pricing) {
            $vehicleType = VehicleType::where('name', $vehicleTypeName)->first();
            if ($vehicleType) {
                ServiceVehiclePrice::updateOrCreate(
                    [
                        'service_id' => $fullService->id,
                        'vehicle_type_id' => $vehicleType->id,
                    ],
                    [
                        'service_price' => $pricing['price'],
                        'service_cost' => $pricing['cost'],
                    ]
                );
            }
        }
    }
}
