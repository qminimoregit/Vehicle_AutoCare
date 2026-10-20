<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\CompanySetting;
use App\Models\ExpenseCategory;
use App\Models\Service;
use App\Models\User;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Initialize Company Settings
        CompanySetting::firstOrCreate([
            'id' => 1,
        ], [
            'company_name' => 'AutoCare Service Center',
            'registration_number' => 'PV-2026-98765',
            'address' => 'No. 124, High Level Road, Colombo, Sri Lanka',
            'phone' => '+94 11 234 5678',
            'email' => 'info@autocare.local',
            'tax_number' => 'VAT-123456789-000',
            'logo_path' => null,
        ]);

        // 2. Initial Vehicle Types
        $vehicleTypes = [
            ['name' => 'Car', 'description' => 'Standard passenger sedans, hatchbacks, and coupes.'],
            ['name' => 'Van', 'description' => 'Light passenger vans and commercial cargo transporters.'],
            ['name' => 'SUV', 'description' => 'Sport utility vehicles, compact crossovers, and 4x4 jeeps.'],
            ['name' => 'Motorbike', 'description' => 'Two-wheel motorcycles, commuters, and motor scooters.'],
            ['name' => 'Lorry', 'description' => 'Light trucks, flatbeds, and commercial transport vehicles.'],
        ];

        foreach ($vehicleTypes as $type) {
            VehicleType::firstOrCreate(
                ['name' => $type['name']],
                [
                    'description' => $type['description'],
                    'status' => 'active',
                ]
            );
        }

        // 3. Initial Core Workshop Services
        $services = [
            [
                'code' => 'SRV-001',
                'name' => 'Full Synthetic Oil & Filter Service',
                'description' => 'Complete engine oil drainage, premium synthetic oil refill, filter change, and general fluids inspection.',
            ],
            [
                'code' => 'SRV-002',
                'name' => 'Brake Inspection & Pad Replacement',
                'description' => 'Hydraulic brake fluid test, caliper cleaning, rotor wear check, and front/rear brake pad replacement.',
            ],
            [
                'code' => 'SRV-003',
                'name' => 'Computerized Wheel Alignment & Balancing',
                'description' => 'Laser 4-wheel geometry alignment, tire tread check, and dynamic wheel balancing.',
            ],
            [
                'code' => 'SRV-004',
                'name' => 'Engine Diagnostic Scan & Tune-Up',
                'description' => 'Comprehensive OBD-II scan, ignition system check, throttle cleaning, and battery health report.',
            ],
            [
                'code' => 'SRV-005',
                'name' => 'Express Wash & Underbody Detailing',
                'description' => 'High pressure chassis wash, shampoo exterior body clean, tire shine, and cabin interior vacuum.',
            ],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(
                ['code' => $service['code']],
                [
                    'name' => $service['name'],
                    'description' => $service['description'],
                    'status' => 'active',
                ]
            );
        }

        // 4. Create a default Staff user for testing staff permissions
        $staffUser = User::firstOrCreate(
            ['email' => 'staff@autocare.com'],
            [
                'name' => 'Kamal Perera',
                'phone' => '+94 77 123 4567',
                'password' => Hash::make('Staff@12345'),
                'role' => 'staff',
                'status' => 'active',
            ]
        );

        // 5. Seed default expense categories (Stage 5)
        ExpenseCategory::seedDefaults();

        ActivityLog::create([
            'user_id' => $staffUser->id,
            'action' => 'seeded',
            'module' => 'System',
            'description' => 'Initial master data records and sample staff account initialized.',
            'ip_address' => '127.0.0.1',
        ]);
    }
}
