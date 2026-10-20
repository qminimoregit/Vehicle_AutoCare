<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceVehiclePrice;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceVehiclePrice>
 */
class ServiceVehiclePriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'vehicle_type_id' => VehicleType::factory(),
            'service_price' => fake()->randomFloat(2, 500, 15000),
            'service_cost' => fake()->randomFloat(2, 200, 5000),
        ];
    }
}
