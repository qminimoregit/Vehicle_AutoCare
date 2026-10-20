<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\DirectSale;
use App\Models\DirectSaleItem;
use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeWageRate;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JobCard;
use App\Models\JobCardProduct;
use App\Models\JobCardService;
use App\Models\Payment;
use App\Models\PayrollItem;
use App\Models\PayrollPayment;
use App\Models\PayrollPeriod;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Service;
use App\Models\ServiceVehiclePrice;
use App\Models\Supplier;
use App\Models\UnitOfMeasurement;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Services\Inventory\StockService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FreshTestDataSeeder extends Seeder
{
    /**
     * Run the fresh test data seeding.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::first();
        $staff = User::where('role', 'staff')->first() ?? $admin;

        $stockService = app(StockService::class);

        // =========================================================================
        // 1. MASTER DATA: Company Settings, Branches, Vehicle Types, Services
        // =========================================================================
        $company = CompanySetting::firstOrCreate([
            'id' => 1,
        ], [
            'company_name' => 'AutoCare Service Center (Pvt) Ltd',
            'registration_number' => 'PV-2026-98765',
            'address' => 'No. 124, High Level Road, Colombo, Sri Lanka',
            'phone' => '+94 11 234 5678',
            'email' => 'info@autocare.local',
            'tax_number' => 'VAT-123456789-000',
        ]);

        // 2 Branches: Main Colombo + Kandy
        $branchColombo = Branch::updateOrCreate(['branch_code' => 'BR-001'], [
            'company_id' => $company->id,
            'branch_name' => 'AutoCare Colombo Main Branch',
            'address' => 'No. 124, High Level Road, Colombo, Sri Lanka',
            'phone' => '+94 11 234 5678',
            'email' => 'colombo@autocare.local',
            'manager_id' => $admin->id,
            'is_main_branch' => true,
            'is_active' => true,
        ]);

        $branchKandy = Branch::updateOrCreate(['branch_code' => 'BR-002'], [
            'company_id' => $company->id,
            'branch_name' => 'AutoCare Kandy Service Center',
            'address' => 'No. 78, William Gopallawa Mawatha, Kandy, Sri Lanka',
            'phone' => '+94 81 223 4567',
            'email' => 'kandy@autocare.local',
            'manager_id' => $admin->id,
            'is_main_branch' => false,
            'is_active' => true,
        ]);

        // Attach Users to both branches
        foreach ([$admin, $staff] as $u) {
            if ($u) {
                $u->branches()->syncWithoutDetaching([$branchColombo->id, $branchKandy->id]);
            }
        }

        // 6 Vehicle Types with images
        $vehicleTypesData = [
            ['name' => 'Car', 'image' => 'vehicle-types/qGqyscnQbYt2CpkgkHH7bpeENNIwVdra2BnV1R4S.png', 'description' => 'Standard passenger sedans, hatchbacks, and coupes.'],
            ['name' => 'Van', 'image' => 'vehicle-types/KbbfsYkekix9AphUzOegMnAVyTocoPBWzD7YzXO9.png', 'description' => 'Light passenger vans and commercial cargo transporters.'],
            ['name' => 'SUV', 'image' => 'vehicle-types/zoL3ws9bJS1sY2qTadqNVAmnadeozlMbc7T3QqQF.png', 'description' => 'Sport utility vehicles, compact crossovers, and 4x4 jeeps.'],
            ['name' => 'Motorbike', 'image' => 'vehicle-types/o56hcpe8BRMP153WVjbRrLnlIAT5Ps1v4R3HWkXN.png', 'description' => 'Two-wheel motorcycles, commuters, and motor scooters.'],
            ['name' => 'Lorry', 'image' => 'vehicle-types/sXxEZpZvlh2B9G0UiDW4ifMBUpAjnGGYMGKFnINZ.png', 'description' => 'Light trucks, flatbeds, and commercial transport vehicles.'],
            ['name' => 'Three-Wheeler', 'image' => 'vehicle-types/lvDxsinxeEgUyIQv0X3SjgLTVDEmKa4Q5J27tZRo.png', 'description' => 'Three-wheel passenger and cargo auto rickshaws.'],
        ];

        $vTypes = [];
        foreach ($vehicleTypesData as $vt) {
            $type = VehicleType::where('name', $vt['name'])->first();
            if ($type) {
                $type->update($vt);
            } else {
                // If 6th existed with name AI Assistant, rename it to Three-Wheeler
                $six = VehicleType::where('id', 6)->first();
                if ($six && $vt['name'] === 'Three-Wheeler') {
                    $six->update($vt);
                    $type = $six;
                } else {
                    $type = VehicleType::create($vt);
                }
            }
            $vTypes[$vt['name']] = $type;
        }

        // 8 Services
        $servicesData = [
            ['code' => 'SRV-001', 'name' => 'Full Synthetic Oil & Filter Service', 'description' => 'Complete engine oil drainage, premium synthetic oil refill, filter change, and general fluids inspection.'],
            ['code' => 'SRV-002', 'name' => 'Brake Inspection & Pad Replacement', 'description' => 'Hydraulic brake fluid test, caliper cleaning, rotor wear check, and front/rear brake pad replacement.'],
            ['code' => 'SRV-003', 'name' => 'Computerized Wheel Alignment & Balancing', 'description' => 'Laser 4-wheel geometry alignment, tire tread check, and dynamic wheel balancing.'],
            ['code' => 'SRV-004', 'name' => 'Engine Diagnostic Scan & Tune-Up', 'description' => 'Comprehensive OBD-II scan, ignition system check, throttle cleaning, and battery health report.'],
            ['code' => 'SRV-005', 'name' => 'Express Wash & Underbody Detailing', 'description' => 'High pressure chassis wash, shampoo exterior body clean, tire shine, and cabin interior vacuum.'],
            ['code' => 'SRV-006', 'name' => 'Automatic Transmission Fluid (ATF) Flush', 'description' => 'Complete automatic transmission fluid exchange, filter replacement, and pressure test.'],
            ['code' => 'SRV-007', 'name' => 'Air Conditioning & Cooling System Service', 'description' => 'A/C gas pressure check, refrigerant recharge, cabin deodorizing, and condenser wash.'],
            ['code' => 'SRV-008', 'name' => 'Suspension & Steering Linkage Overhaul', 'description' => 'Shock absorber test, ball joint and tie rod inspection, bushing lubrication, and road testing.'],
        ];

        $services = [];
        foreach ($servicesData as $sd) {
            $srv = Service::where('code', $sd['code'])->first();
            if ($srv) {
                $srv->update($sd);
            } else {
                $srv = Service::create($sd);
            }
            $services[$sd['code']] = $srv;
        }

        // Service Pricing per Vehicle Type
        $pricingMatrix = [
            'SRV-001' => ['Car' => [3500, 1000], 'Van' => [5000, 1500], 'SUV' => [6200, 2000], 'Motorbike' => [1200, 400], 'Lorry' => [8000, 3000], 'Three-Wheeler' => [1500, 500]],
            'SRV-002' => ['Car' => [2500, 800],  'Van' => [3500, 1200], 'SUV' => [4500, 1500], 'Motorbike' => [800, 250],  'Lorry' => [6000, 2000], 'Three-Wheeler' => [1000, 350]],
            'SRV-003' => ['Car' => [2000, 500],  'Van' => [3000, 800],  'SUV' => [3500, 1000], 'Motorbike' => [600, 150],  'Lorry' => [4500, 1200], 'Three-Wheeler' => [800, 200]],
            'SRV-004' => ['Car' => [3000, 900],  'Van' => [4000, 1200], 'SUV' => [5000, 1500], 'Motorbike' => [1000, 300], 'Lorry' => [6500, 2200], 'Three-Wheeler' => [1200, 400]],
            'SRV-005' => ['Car' => [1500, 400],  'Van' => [2200, 600],  'SUV' => [2800, 800],  'Motorbike' => [500, 150],  'Lorry' => [4000, 1200], 'Three-Wheeler' => [700, 200]],
            'SRV-006' => ['Car' => [4500, 1500], 'Van' => [6000, 2000], 'SUV' => [7500, 2500], 'Motorbike' => [1500, 500], 'Lorry' => [9500, 3500], 'Three-Wheeler' => [1800, 600]],
            'SRV-007' => ['Car' => [3800, 1200], 'Van' => [4800, 1600], 'SUV' => [5800, 2000], 'Motorbike' => [900, 300],  'Lorry' => [7000, 2500], 'Three-Wheeler' => [1200, 400]],
            'SRV-008' => ['Car' => [5500, 1800], 'Van' => [7500, 2500], 'SUV' => [9000, 3000], 'Motorbike' => [1800, 600], 'Lorry' => [12000, 4500], 'Three-Wheeler' => [2200, 800]],
        ];

        foreach ($pricingMatrix as $sCode => $typeRates) {
            $srv = $services[$sCode];
            foreach ($typeRates as $tName => [$price, $cost]) {
                if (isset($vTypes[$tName])) {
                    ServiceVehiclePrice::updateOrCreate(
                        ['service_id' => $srv->id, 'vehicle_type_id' => $vTypes[$tName]->id],
                        ['service_price' => $price, 'service_cost' => $cost]
                    );
                }
            }
        }

        // =========================================================================
        // 2. SUPPLIERS & INVENTORY (UOMs, Suppliers, Products, GRNs & Batches)
        // =========================================================================
        $uomBottle = UnitOfMeasurement::firstOrCreate(['code' => 'BTL'], ['name' => 'Bottle', 'is_active' => true]);
        $uomCan = UnitOfMeasurement::firstOrCreate(['code' => 'CAN'], ['name' => 'Can', 'is_active' => true]);
        $uomPacket = UnitOfMeasurement::firstOrCreate(['code' => 'PKT'], ['name' => 'Packet', 'is_active' => true]);
        $uomItem = UnitOfMeasurement::firstOrCreate(['code' => 'ITM'], ['name' => 'Item', 'is_active' => true]);

        // 5 Suppliers
        $suppliersData = [
            ['name' => 'Mobil Lubricants Lanka (Pvt) Ltd', 'registration_number' => 'PV-MOBIL-101', 'address' => 'No. 320, T.B. Jaya Mawatha, Colombo 10', 'mobile' => '0772233441', 'office_phone' => '0112445501'],
            ['name' => 'Denso Auto Parts Ceylon',          'registration_number' => 'PV-DENSO-202', 'address' => 'No. 88, Panchikawatta Road, Colombo 10', 'mobile' => '0714455662', 'office_phone' => '0112889902'],
            ['name' => 'Ferodo Brake Components Ltd',     'registration_number' => 'PV-FEROD-303', 'address' => 'No. 45, Biyagama Export Zone, Kelaniya',  'mobile' => '0765566773', 'office_phone' => '0112443303'],
            ['name' => 'Bosch Automotive Sri Lanka',       'registration_number' => 'PV-BOSCH-404', 'address' => 'No. 12, Union Place, Colombo 02',         'mobile' => '0726677884', 'office_phone' => '0112667704'],
            ['name' => 'Wurth Lanka (Pvt) Ltd',            'registration_number' => 'PV-WURTH-505', 'address' => 'No. 375, Negombo Road, Wattala',          'mobile' => '0757788995', 'office_phone' => '0112998805'],
        ];

        $suppliers = [];
        foreach ($suppliersData as $sup) {
            $suppliers[] = Supplier::firstOrCreate(['registration_number' => $sup['registration_number']], $sup);
        }

        // 15 Products
        $productsData = [
            ['sku' => 'PRD-MOB-001', 'name' => 'Mobil 1 5W-30 Full Synthetic Engine Oil 4L', 'uom_id' => $uomBottle->id, 'preferred_supplier_id' => $suppliers[0]->id, 'min_stock' => 5, 'max_stock' => 50, 'selling_price' => 14500.00],
            ['sku' => 'PRD-MOB-002', 'name' => 'Mobil Super 10W-40 Synthetic Blend Oil 4L',   'uom_id' => $uomCan->id,    'preferred_supplier_id' => $suppliers[0]->id, 'min_stock' => 5, 'max_stock' => 40, 'selling_price' => 11200.00],
            ['sku' => 'PRD-DEN-003', 'name' => 'Denso Premium Engine Oil Filter - Toyota/Nissan', 'uom_id' => $uomItem->id, 'preferred_supplier_id' => $suppliers[1]->id, 'min_stock' => 10, 'max_stock' => 60, 'selling_price' => 2800.00],
            ['sku' => 'PRD-DEN-004', 'name' => 'Denso High Flow Air Filter',                  'uom_id' => $uomItem->id,   'preferred_supplier_id' => $suppliers[1]->id, 'min_stock' => 8, 'max_stock' => 50, 'selling_price' => 3500.00],
            ['sku' => 'PRD-FER-005', 'name' => 'Ferodo Ceramic Front Brake Pads - Car/SUV',   'uom_id' => $uomItem->id,   'preferred_supplier_id' => $suppliers[2]->id, 'min_stock' => 6, 'max_stock' => 30, 'selling_price' => 9500.00],
            ['sku' => 'PRD-FER-006', 'name' => 'Ferodo Heavy Duty Rear Brake Shoes - Van/Lorry', 'uom_id' => $uomItem->id, 'preferred_supplier_id' => $suppliers[2]->id, 'min_stock' => 5, 'max_stock' => 25, 'selling_price' => 12000.00],
            ['sku' => 'PRD-BOS-007', 'name' => 'Bosch S4 Mega Power 12V 65Ah Maintenance-Free Battery', 'uom_id' => $uomItem->id, 'preferred_supplier_id' => $suppliers[3]->id, 'min_stock' => 4, 'max_stock' => 20, 'selling_price' => 36000.00],
            ['sku' => 'PRD-BOS-008', 'name' => 'Bosch Laser Iridium Spark Plug Set (4-Pack)', 'uom_id' => $uomPacket->id, 'preferred_supplier_id' => $suppliers[3]->id, 'min_stock' => 8, 'max_stock' => 40, 'selling_price' => 7200.00],
            ['sku' => 'PRD-WUR-009', 'name' => 'Wurth DOT 4 High Performance Brake Fluid 500ml', 'uom_id' => $uomBottle->id, 'preferred_supplier_id' => $suppliers[4]->id, 'min_stock' => 10, 'max_stock' => 50, 'selling_price' => 2400.00],
            ['sku' => 'PRD-WUR-010', 'name' => 'Wurth Long-Life Radiator Coolant 1L',         'uom_id' => $uomBottle->id, 'preferred_supplier_id' => $suppliers[4]->id, 'min_stock' => 10, 'max_stock' => 50, 'selling_price' => 2900.00],
            ['sku' => 'PRD-BOS-011', 'name' => 'Bosch Aerotwin Premium Wiper Blade Set',      'uom_id' => $uomItem->id,   'preferred_supplier_id' => $suppliers[3]->id, 'min_stock' => 8, 'max_stock' => 30, 'selling_price' => 4800.00],
            ['sku' => 'PRD-WUR-012', 'name' => 'Wurth Multi-Purpose Penetrating Spray 400ml', 'uom_id' => $uomCan->id,    'preferred_supplier_id' => $suppliers[4]->id, 'min_stock' => 10, 'max_stock' => 40, 'selling_price' => 2100.00],
            ['sku' => 'PRD-DEN-013', 'name' => 'Denso Cabin A/C Charcoal Air Filter',         'uom_id' => $uomItem->id,   'preferred_supplier_id' => $suppliers[1]->id, 'min_stock' => 6, 'max_stock' => 30, 'selling_price' => 3200.00],
            ['sku' => 'PRD-GAT-014', 'name' => 'Gates V-Ribbed Serpentine Fan Belt',          'uom_id' => $uomItem->id,   'preferred_supplier_id' => $suppliers[3]->id, 'min_stock' => 5, 'max_stock' => 25, 'selling_price' => 4500.00], // ZERO STOCK
            ['sku' => 'PRD-OSR-015', 'name' => 'Osram H4 12V 60/55W Halogen Headlight Bulb',   'uom_id' => $uomItem->id,   'preferred_supplier_id' => $suppliers[3]->id, 'min_stock' => 10, 'max_stock' => 50, 'selling_price' => 1800.00], // LOW STOCK
        ];

        $products = [];
        foreach ($productsData as $pd) {
            $p = Product::where('sku', $pd['sku'])->first();
            if ($p) {
                $p->update($pd);
            } else {
                $p = Product::create($pd);
            }
            $products[$pd['sku']] = $p;
        }

        // 6 Goods Received Notes (GRNs) confirmed through StockService
        $grnsData = [
            // GRN 1: Mobil Lubricants (Batch 1 for Mobil 1 5W-30 @ 9500 LKR, Super 10W-40 @ 7800 LKR)
            [
                'number' => 'GRN-202609-0001',
                'supplier' => $suppliers[0],
                'date' => Carbon::now()->subDays(45)->toDateString(),
                'invoice_no' => 'INV-MOB-8891',
                'items' => [
                    ['product' => $products['PRD-MOB-001'], 'qty' => 20, 'free' => 0, 'cost' => 9500.00],
                    ['product' => $products['PRD-MOB-002'], 'qty' => 15, 'free' => 0, 'cost' => 7800.00],
                ],
            ],
            // GRN 2: Denso Auto Parts
            [
                'number' => 'GRN-202609-0002',
                'supplier' => $suppliers[1],
                'date' => Carbon::now()->subDays(40)->toDateString(),
                'invoice_no' => 'INV-DEN-5542',
                'items' => [
                    ['product' => $products['PRD-DEN-003'], 'qty' => 30, 'free' => 2, 'cost' => 1800.00],
                    ['product' => $products['PRD-DEN-004'], 'qty' => 25, 'free' => 0, 'cost' => 2200.00],
                    ['product' => $products['PRD-DEN-013'], 'qty' => 15, 'free' => 0, 'cost' => 2000.00],
                ],
            ],
            // GRN 3: Ferodo Brake Components
            [
                'number' => 'GRN-202609-0003',
                'supplier' => $suppliers[2],
                'date' => Carbon::now()->subDays(35)->toDateString(),
                'invoice_no' => 'INV-FER-3310',
                'items' => [
                    ['product' => $products['PRD-FER-005'], 'qty' => 18, 'free' => 0, 'cost' => 6200.00],
                    ['product' => $products['PRD-FER-006'], 'qty' => 12, 'free' => 0, 'cost' => 8000.00],
                ],
            ],
            // GRN 4: Bosch Automotive
            [
                'number' => 'GRN-202609-0004',
                'supplier' => $suppliers[3],
                'date' => Carbon::now()->subDays(30)->toDateString(),
                'invoice_no' => 'INV-BOS-9901',
                'items' => [
                    ['product' => $products['PRD-BOS-007'], 'qty' => 10, 'free' => 0, 'cost' => 26000.00],
                    ['product' => $products['PRD-BOS-008'], 'qty' => 20, 'free' => 0, 'cost' => 4800.00],
                    ['product' => $products['PRD-BOS-011'], 'qty' => 15, 'free' => 0, 'cost' => 3100.00],
                    ['product' => $products['PRD-OSR-015'], 'qty' => 4,  'free' => 0, 'cost' => 1100.00], // Low stock baseline
                ],
            ],
            // GRN 5: Wurth Lanka
            [
                'number' => 'GRN-202609-0005',
                'supplier' => $suppliers[4],
                'date' => Carbon::now()->subDays(25)->toDateString(),
                'invoice_no' => 'INV-WUR-7721',
                'items' => [
                    ['product' => $products['PRD-WUR-009'], 'qty' => 25, 'free' => 0, 'cost' => 1500.00],
                    ['product' => $products['PRD-WUR-010'], 'qty' => 20, 'free' => 0, 'cost' => 1800.00],
                    ['product' => $products['PRD-WUR-012'], 'qty' => 20, 'free' => 0, 'cost' => 1300.00],
                ],
            ],
            // GRN 6: Mobil Lubricants - NEW BATCH (Batch 2 for Mobil 1 5W-30 @ 9800 LKR - higher cost to test FIFO!)
            [
                'number' => 'GRN-202610-0006',
                'supplier' => $suppliers[0],
                'date' => Carbon::now()->subDays(10)->toDateString(),
                'invoice_no' => 'INV-MOB-9944',
                'items' => [
                    ['product' => $products['PRD-MOB-001'], 'qty' => 15, 'free' => 0, 'cost' => 9800.00],
                ],
            ],
        ];

        foreach ($grnsData as $gData) {
            $totalAmount = 0;
            foreach ($gData['items'] as $item) {
                $totalAmount += $item['qty'] * $item['cost'];
            }

            $grn = GoodsReceivedNote::create([
                'grn_number' => $gData['number'],
                'branch_id' => $branchColombo->id,
                'supplier_id' => $gData['supplier']->id,
                'supplier_invoice_number' => $gData['invoice_no'],
                'received_date' => $gData['date'],
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'notes' => 'Received verified auto care inventory batch.',
                'created_by' => $admin->id,
            ]);

            foreach ($gData['items'] as $item) {
                GoodsReceivedNoteItem::create([
                    'grn_id' => $grn->id,
                    'product_id' => $item['product']->id,
                    'purchase_quantity' => $item['qty'],
                    'free_quantity' => $item['free'],
                    'purchase_unit_price' => $item['cost'],
                    'selling_price_snapshot' => $item['product']->selling_price,
                    'line_total' => $item['qty'] * $item['cost'],
                ]);
            }

            // Confirm GRN to create StockBatches & StockMovements via StockService
            $stockService->confirmGrn($grn, $admin);
        }

        // =========================================================================
        // 3. CUSTOMERS & VEHICLES (8 Individuals + 2 Companies, 15 Vehicles)
        // =========================================================================
        $customersData = [
            // Individuals
            ['customer_name' => 'Sunil Shantha',       'phone' => '0772345671', 'email' => 'sunil.shantha@gmail.com',       'customer_type' => 'individual', 'address' => 'No. 45, Galle Road, Colombo 03'],
            ['customer_name' => 'Nimal Jayasinghe',     'phone' => '0713456782', 'email' => 'nimal.jayasinghe@gmail.com',   'customer_type' => 'individual', 'address' => 'No. 12, Peradeniya Road, Kandy'],
            ['customer_name' => 'Kasun Bandara',       'phone' => '0754567893', 'email' => 'kasun.bandara@gmail.com',       'customer_type' => 'individual', 'address' => 'No. 88, Kandy Road, Gampaha'],
            ['customer_name' => 'Chaminda Silva',      'phone' => '0765678904', 'email' => 'chaminda.silva@gmail.com',      'customer_type' => 'individual', 'address' => 'No. 24, Hill Street, Dehiwala'],
            ['customer_name' => 'Priyanka Fernando',   'phone' => '0726789015', 'email' => 'priyanka.fernando@gmail.com',   'customer_type' => 'individual', 'address' => 'No. 102, Main Street, Negombo'],
            ['customer_name' => 'Dilshan Perera',      'phone' => '0787890126', 'email' => 'dilshan.perera@gmail.com',      'customer_type' => 'individual', 'address' => 'No. 15, Old Road, Moratuwa'],
            ['customer_name' => 'Ruwan Wickramasinghe', 'phone' => '0708901237', 'email' => 'ruwan.wick@gmail.com',          'customer_type' => 'individual', 'address' => 'No. 67, Waragoda Road, Kelaniya'],
            ['customer_name' => 'Nadeeka Damayanthi',  'phone' => '0749012348', 'email' => 'nadeeka.damayanthi@gmail.com',  'customer_type' => 'individual', 'address' => 'No. 33, Parliament Road, Battaramulla'],
            // Companies
            ['customer_name' => 'Roshan De Silva',     'phone' => '0112445566', 'email' => 'fleet@quickcab.lk',             'customer_type' => 'company',    'company_name' => 'QuickCab Fleet Services (Pvt) Ltd', 'registration_number' => 'PV-QC-9988', 'tax_number' => 'VAT-987654321-001', 'address' => 'No. 500, Nawala Road, Rajagiriya'],
            ['customer_name' => 'Anura Basnayake',    'phone' => '0112778899', 'email' => 'logistics@lankalogistics.lk',   'customer_type' => 'company',    'company_name' => 'Lanka Logistics & Distribution Ltd', 'registration_number' => 'PV-LL-7744', 'tax_number' => 'VAT-456789123-002', 'address' => 'No. 200, Baseline Road, Colombo 09'],
        ];

        $customers = [];
        foreach ($customersData as $cd) {
            $customers[] = Customer::create(array_merge($cd, ['status' => 'active', 'sms_consent' => true]));
        }

        // 15 Registered Vehicles
        $vehiclesData = [
            // Sunil Shantha (2 vehicles)
            ['customer' => $customers[0], 'type' => $vTypes['Car'], 'num' => 'WP-CAB-5544', 'model' => 'Toyota Prius Hybrid 1.8L', 'year' => 2017, 'color' => 'Pearl White', 'mileage' => 84500],
            ['customer' => $customers[0], 'type' => $vTypes['Car'], 'num' => 'WP-CAR-1289', 'model' => 'Honda Fit GP5 Hybrid',     'year' => 2015, 'color' => 'Silver Metallic', 'mileage' => 96200],
            // Nimal Jayasinghe (Kandy Branch)
            ['customer' => $customers[1], 'type' => $vTypes['Car'], 'num' => 'CP-KH-8921', 'model' => 'Toyota Corolla 141 GLI',   'year' => 2011, 'color' => 'Dark Grey', 'mileage' => 135000, 'branch' => $branchKandy],
            // QuickCab Fleet (3 vehicles)
            ['customer' => $customers[8], 'type' => $vTypes['Van'], 'num' => 'WP-KX-7788', 'model' => 'Toyota HiAce KDH 201',      'year' => 2018, 'color' => 'White', 'mileage' => 152000],
            ['customer' => $customers[8], 'type' => $vTypes['Van'], 'num' => 'WP-KY-3321', 'model' => 'Nissan Caravan NV350',      'year' => 2019, 'color' => 'Silver', 'mileage' => 128400],
            ['customer' => $customers[8], 'type' => $vTypes['Car'], 'num' => 'WP-CAR-9090', 'model' => 'Suzuki Wagon R Stingray FX', 'year' => 2018, 'color' => 'Black', 'mileage' => 74100],
            // Lanka Logistics (3 vehicles)
            ['customer' => $customers[9], 'type' => $vTypes['Lorry'], 'num' => 'WP-LY-4455', 'model' => 'Isuzu ELF 14.5ft Flatbed', 'year' => 2016, 'color' => 'White', 'mileage' => 189000],
            ['customer' => $customers[9], 'type' => $vTypes['Lorry'], 'num' => 'WP-LZ-6677', 'model' => 'Mitsubishi Fuso Canter 16ft', 'year' => 2017, 'color' => 'Blue', 'mileage' => 164000],
            ['customer' => $customers[9], 'type' => $vTypes['Van'],   'num' => 'CP-LE-1122', 'model' => 'Toyota HiAce Super GL',    'year' => 2015, 'color' => 'Silver', 'mileage' => 141000, 'branch' => $branchKandy],
            // Kasun Bandara
            ['customer' => $customers[2], 'type' => $vTypes['SUV'],   'num' => 'SP-BC-4412', 'model' => 'Mitsubishi Montero Sport 4WD', 'year' => 2016, 'color' => 'Titanium Grey', 'mileage' => 112000],
            // Chaminda Silva
            ['customer' => $customers[3], 'type' => $vTypes['SUV'],   'num' => 'WP-CAD-8822', 'model' => 'Toyota Land Cruiser Prado TX', 'year' => 2019, 'color' => 'Black Mica', 'mileage' => 68000],
            // Priyanka Fernando
            ['customer' => $customers[4], 'type' => $vTypes['Motorbike'], 'num' => 'WP-BHM-9911', 'model' => 'Yamaha FZ-S V3 150cc', 'year' => 2021, 'color' => 'Matte Blue', 'mileage' => 24500],
            // Dilshan Perera
            ['customer' => $customers[5], 'type' => $vTypes['Motorbike'], 'num' => 'WP-BIP-4567', 'model' => 'Honda Dio 110cc Scooter', 'year' => 2020, 'color' => 'Sports Red', 'mileage' => 31200],
            // Ruwan Wickramasinghe - Fresh vehicle (NO service history)
            ['customer' => $customers[6], 'type' => $vTypes['Three-Wheeler'], 'num' => 'WP-AAH-3344', 'model' => 'Bajaj RE 4S 205cc Auto Rickshaw', 'year' => 2022, 'color' => 'Golden Yellow', 'mileage' => 18000],
            // Nadeeka Damayanthi - Fresh vehicle (NO service history)
            ['customer' => $customers[7], 'type' => $vTypes['Car'], 'num' => 'WP-CBB-1100', 'model' => 'Toyota Vitz KSP130 1.0L', 'year' => 2019, 'color' => 'Wine Red', 'mileage' => 45000],
        ];

        $vehicles = [];
        foreach ($vehiclesData as $vd) {
            $vBranch = $vd['branch'] ?? $branchColombo;
            $vehicles[$vd['num']] = Vehicle::create([
                'customer_id' => $vd['customer']->id,
                'branch_id' => $vBranch->id,
                'vehicle_type_id' => $vd['type']->id,
                'vehicle_number' => $vd['num'],
                'vehicle_number_normalized' => Vehicle::normalizeNumber($vd['num']),
                'make_model' => $vd['model'],
                'year_of_manufacture' => $vd['year'],
                'color' => $vd['color'],
                'current_mileage' => $vd['mileage'],
                'next_service_mileage' => $vd['mileage'] + 5000,
                'notes' => 'Registered for routine workshop servicing.',
            ]);
        }

        // =========================================================================
        // 4. JOB CARDS (12 total: 3 Pending, 3 In-Progress, 4 Completed, 2 Delivered)
        // =========================================================================
        $jobCardsData = [
            // 3 Pending
            ['num' => 'JOB-202610-0001', 'status' => 'pending', 'v' => $vehicles['WP-CAR-1289'], 'srvs' => ['SRV-004'], 'parts' => []],
            ['num' => 'JOB-202610-0002', 'status' => 'pending', 'v' => $vehicles['WP-KY-3321'],  'srvs' => ['SRV-003'], 'parts' => []],
            ['num' => 'JOB-202610-0003', 'status' => 'pending', 'v' => $vehicles['WP-LY-4455'],  'srvs' => ['SRV-002'], 'parts' => []],

            // 3 In-Progress
            ['num' => 'JOB-202610-0004', 'status' => 'in_progress', 'v' => $vehicles['SP-BC-4412'],  'srvs' => ['SRV-008'], 'parts' => []],
            ['num' => 'JOB-202610-0005', 'status' => 'in_progress', 'v' => $vehicles['WP-CAD-8822'], 'srvs' => ['SRV-007'], 'parts' => []],
            ['num' => 'JOB-202610-0006', 'status' => 'in_progress', 'v' => $vehicles['WP-CAR-9090'], 'srvs' => ['SRV-001'], 'parts' => [['prd' => $products['PRD-DEN-003'], 'qty' => 1]]],

            // 4 Completed
            ['num' => 'JOB-202610-0007', 'status' => 'completed', 'v' => $vehicles['CP-KH-8921'],  'srvs' => ['SRV-002'], 'parts' => [['prd' => $products['PRD-FER-005'], 'qty' => 1]], 'branch' => $branchKandy],
            ['num' => 'JOB-202610-0008', 'status' => 'completed', 'v' => $vehicles['WP-KX-7788'],  'srvs' => ['SRV-001', 'SRV-003'], 'parts' => [['prd' => $products['PRD-MOB-001'], 'qty' => 1], ['prd' => $products['PRD-DEN-003'], 'qty' => 1]]],
            ['num' => 'JOB-202610-0009', 'status' => 'completed', 'v' => $vehicles['WP-LZ-6677'],  'srvs' => ['SRV-002'], 'parts' => [['prd' => $products['PRD-FER-006'], 'qty' => 1]]],
            // Target for Stage 6 Avoid Service Sale
            ['num' => 'JOB-202610-0010', 'status' => 'completed', 'v' => $vehicles['WP-CAB-5544'],  'srvs' => ['SRV-001'], 'parts' => [['prd' => $products['PRD-DEN-003'], 'qty' => 1]]],

            // 2 Delivered (with paid Invoices)
            ['num' => 'JOB-202610-0011', 'status' => 'delivered', 'v' => $vehicles['WP-BHM-9911'], 'srvs' => ['SRV-004'], 'parts' => [['prd' => $products['PRD-BOS-008'], 'qty' => 1]]],
            ['num' => 'JOB-202610-0012', 'status' => 'delivered', 'v' => $vehicles['WP-BIP-4567'], 'srvs' => ['SRV-005'], 'parts' => []],
        ];

        $jobCards = [];
        foreach ($jobCardsData as $jd) {
            $v = $jd['v'];
            $jBranch = $jd['branch'] ?? $v->branch ?? $branchColombo;

            $job = JobCard::create([
                'job_number' => $jd['num'],
                'branch_id' => $jBranch->id,
                'date' => Carbon::now()->subDays(rand(1, 15))->toDateString(),
                'customer_id' => $v->customer_id,
                'vehicle_id' => $v->id,
                'vehicle_type_id' => $v->vehicle_type_id,
                'current_mileage' => $v->current_mileage,
                'next_service_mileage' => $v->next_service_mileage,
                'status' => $jd['status'],
                'assigned_to' => $admin->id,
                'created_by' => $admin->id,
                'started_at' => in_array($jd['status'], ['in_progress', 'completed', 'delivered']) ? now()->subDays(2) : null,
                'completed_at' => in_array($jd['status'], ['completed', 'delivered']) ? now()->subDay() : null,
                'delivered_at' => $jd['status'] === 'delivered' ? now() : null,
            ]);

            $srvTotal = 0;
            foreach ($jd['srvs'] as $sCode) {
                $srv = $services[$sCode];
                $rateRow = ServiceVehiclePrice::where('service_id', $srv->id)
                    ->where('vehicle_type_id', $v->vehicle_type_id)
                    ->first();
                $price = $rateRow ? (float) $rateRow->service_price : 3500.00;

                JobCardService::create([
                    'job_card_id' => $job->id,
                    'service_id' => $srv->id,
                    'service_code' => $srv->code,
                    'service_name' => $srv->name,
                    'unit_price' => $price,
                    'quantity' => 1,
                    'line_total' => $price,
                    'technician_name' => 'Nilantha Kumara',
                ]);
                $srvTotal += $price;
            }

            $partTotal = 0;
            foreach ($jd['parts'] as $part) {
                $prd = $part['prd'];
                $qty = $part['qty'];
                $unitPrice = (float) $prd->selling_price;
                $lineTotal = $unitPrice * $qty;

                // FIFO issue stock
                $issueResult = $stockService->issueStockFifo($prd, (float) $qty, 'job_card', $job, $admin);

                JobCardProduct::create([
                    'job_card_id' => $job->id,
                    'product_id' => $prd->id,
                    'product_sku' => $prd->sku,
                    'product_name' => $prd->name,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'unit_cost' => $issueResult['total_cost'] / $qty,
                    'line_total' => $lineTotal,
                    'is_stock_deducted' => true,
                    'batch_allocations' => $issueResult['batches'],
                ]);
                $partTotal += $lineTotal;
            }

            $grandTotal = $srvTotal + $partTotal;
            $job->update([
                'service_total' => $srvTotal,
                'product_total' => $partTotal,
                'grand_total' => $grandTotal,
            ]);

            $jobCards[$jd['num']] = $job;
        }

        // =========================================================================
        // 5. DIRECT SALES (5 sales: Cash, Card, Bank, and Stage 6 Avoid Direct Sale)
        // =========================================================================
        $directSalesData = [
            // DS 1: Cash sale to Kasun Bandara
            ['num' => 'DS-202610-0001', 'cust' => $customers[2], 'method' => 'cash', 'items' => [['prd' => $products['PRD-MOB-002'], 'qty' => 1], ['prd' => $products['PRD-DEN-003'], 'qty' => 1]]],
            // DS 2: Card sale to Chaminda Silva
            ['num' => 'DS-202610-0002', 'cust' => $customers[3], 'method' => 'card', 'items' => [['prd' => $products['PRD-BOS-011'], 'qty' => 1], ['prd' => $products['PRD-WUR-012'], 'qty' => 1]]],
            // DS 3: Bank transfer to Lanka Logistics
            ['num' => 'DS-202610-0003', 'cust' => $customers[9], 'method' => 'bank_transfer', 'items' => [['prd' => $products['PRD-OSR-015'], 'qty' => 2], ['prd' => $products['PRD-BOS-008'], 'qty' => 1]]],
            // DS 4: Cash sale to Ruwan Wickramasinghe
            ['num' => 'DS-202610-0004', 'cust' => $customers[6], 'method' => 'cash', 'items' => [['prd' => $products['PRD-WUR-009'], 'qty' => 1]]],
            // DS 5: Card sale to Sunil Shantha - Target for Stage 6 Avoid Direct Sale
            ['num' => 'DS-202610-0005', 'cust' => $customers[0], 'method' => 'card', 'items' => [['prd' => $products['PRD-MOB-001'], 'qty' => 1], ['prd' => $products['PRD-DEN-004'], 'qty' => 1]]],
        ];

        $directSales = [];
        foreach ($directSalesData as $dsData) {
            $cust = $dsData['cust'];

            $sale = DirectSale::create([
                'sale_number' => $dsData['num'],
                'branch_id' => $branchColombo->id,
                'customer_id' => $cust->id,
                'sale_date' => Carbon::now()->subDays(rand(1, 5))->toDateString(),
                'payment_method' => $dsData['method'],
                'status' => 'completed',
                'created_by' => $admin->id,
                'subtotal' => 0,
                'grand_total' => 0,
                'amount_paid' => 0,
                'change_amount' => 0,
            ]);

            $subtotal = 0;
            foreach ($dsData['items'] as $item) {
                $prd = $item['prd'];
                $qty = $item['qty'];
                $unitPrice = (float) $prd->selling_price;
                $lineTotal = $unitPrice * $qty;

                // FIFO deduction
                $issueResult = $stockService->issueStockFifo($prd, (float) $qty, 'direct_sale', $sale, $admin);

                DirectSaleItem::create([
                    'direct_sale_id' => $sale->id,
                    'product_id' => $prd->id,
                    'product_sku' => $prd->sku,
                    'product_name' => $prd->name,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'unit_cost' => $issueResult['total_cost'] / $qty,
                    'line_total' => $lineTotal,
                    'batch_allocations' => $issueResult['batches'],
                ]);
                $subtotal += $lineTotal;
            }

            $sale->update([
                'subtotal' => $subtotal,
                'grand_total' => $subtotal,
                'amount_paid' => $subtotal,
            ]);

            $directSales[$dsData['num']] = $sale;
        }

        // =========================================================================
        // 6. QUOTATIONS & INVOICES (5 Quotations, 8 Invoices covering all Stage 6 tests)
        // =========================================================================
        // Quotations
        $quotationsData = [
            ['num' => 'QT-202610-0001', 'cust' => $customers[0], 'v' => $vehicles['WP-CAR-1289'], 'status' => 'draft', 'subject' => 'Major 100,000 km Hybrid Service Estimate'],
            ['num' => 'QT-202610-0002', 'cust' => $customers[1], 'v' => $vehicles['CP-KH-8921'],  'status' => 'sent',  'subject' => 'Brake & Suspension Replacement Estimate'],
            ['num' => 'QT-202610-0003', 'cust' => $customers[8], 'v' => $vehicles['WP-KX-7788'],  'status' => 'accepted', 'subject' => 'Corporate Van Fleet Maintenance Contract'],
            ['num' => 'QT-202610-0004', 'cust' => $customers[3], 'v' => $vehicles['WP-CAD-8822'], 'status' => 'rejected', 'subject' => 'A/C Evaporator Replacement Estimate'],
            ['num' => 'QT-202610-0005', 'cust' => $customers[4], 'v' => $vehicles['WP-BHM-9911'], 'status' => 'converted', 'subject' => 'Motorbike Full Tune-up and Spark Plugs'],
        ];

        foreach ($quotationsData as $qd) {
            $q = Quotation::create([
                'branch_id' => $branchColombo->id,
                'customer_id' => $qd['cust']->id,
                'vehicle_id' => $qd['v']->id,
                'quotation_number' => $qd['num'],
                'quotation_date' => Carbon::now()->subDays(rand(3, 15))->toDateString(),
                'expiry_date' => Carbon::now()->addDays(15)->toDateString(),
                'subject' => $qd['subject'],
                'status' => $qd['status'],
                'subtotal' => 12500.00,
                'grand_total' => 12500.00,
                'created_by' => $admin->id,
            ]);

            QuotationItem::create([
                'quotation_id' => $q->id,
                'item_type' => 'service',
                'item_name' => 'Preventive Inspection & Tune-up',
                'quantity' => 1,
                'unit_price' => 5000.00,
                'line_total' => 5000.00,
            ]);

            QuotationItem::create([
                'quotation_id' => $q->id,
                'item_type' => 'product',
                'item_name' => 'Bosch Laser Iridium Spark Plug Set',
                'quantity' => 1,
                'unit_price' => 7500.00,
                'line_total' => 7500.00,
            ]);
        }

        // Invoices: 8 Invoices
        // INV 1: Delivered Job Card JOB-11 (Paid)
        $inv1 = Invoice::create([
            'invoice_number' => 'INV-202610-0001',
            'branch_id' => $branchColombo->id,
            'customer_id' => $customers[4]->id,
            'invoice_type' => 'service',
            'invoice_date' => Carbon::now()->subDays(5)->toDateString(),
            'subtotal' => $jobCards['JOB-202610-0011']->grand_total,
            'grand_total' => $jobCards['JOB-202610-0011']->grand_total,
            'amount_paid' => $jobCards['JOB-202610-0011']->grand_total,
            'balance_amount' => 0.00,
            'status' => 'paid',
            'created_by' => $admin->id,
        ]);
        $jobCards['JOB-202610-0011']->update(['invoice_id' => $inv1->id]);
        Payment::create([
            'invoice_id' => $inv1->id,
            'customer_id' => $inv1->customer_id,
            'payment_number' => 'PAY-202610-0001',
            'payment_date' => $inv1->invoice_date,
            'amount' => $inv1->grand_total,
            'payment_method' => 'cash',
            'status' => 'completed',
            'created_by' => $admin->id,
        ]);

        // INV 2: QuickCab Fleet Services (Unpaid - 18,500 LKR) -> Stage 6 Bulk Payment Pool
        $inv2 = Invoice::create([
            'invoice_number' => 'INV-202610-0002',
            'branch_id' => $branchColombo->id,
            'customer_id' => $customers[8]->id, // QuickCab
            'invoice_type' => 'service',
            'invoice_date' => Carbon::now()->subDays(8)->toDateString(),
            'subtotal' => 18500.00,
            'grand_total' => 18500.00,
            'amount_paid' => 0.00,
            'balance_amount' => 18500.00,
            'status' => 'issued',
            'created_by' => $admin->id,
        ]);
        $jobCards['JOB-202610-0008']->update(['invoice_id' => $inv2->id]);
        InvoiceItem::create([
            'invoice_id' => $inv2->id,
            'item_type' => 'service',
            'item_name' => 'Van Fleet Full Lubrication & Alignment',
            'quantity' => 1,
            'unit_price' => 18500.00,
            'line_total' => 18500.00,
        ]);

        // INV 3: QuickCab Fleet Services (Unpaid - 14,800 LKR) -> Stage 6 Bulk Payment Pool
        $inv3 = Invoice::create([
            'invoice_number' => 'INV-202610-0003',
            'branch_id' => $branchColombo->id,
            'customer_id' => $customers[8]->id, // QuickCab
            'invoice_type' => 'direct_sale',
            'invoice_date' => Carbon::now()->subDays(6)->toDateString(),
            'subtotal' => 14800.00,
            'grand_total' => 14800.00,
            'amount_paid' => 0.00,
            'balance_amount' => 14800.00,
            'status' => 'issued',
            'created_by' => $admin->id,
        ]);
        InvoiceItem::create([
            'invoice_id' => $inv3->id,
            'item_type' => 'product',
            'item_name' => 'Emergency Battery & Bulbs Supply',
            'quantity' => 1,
            'unit_price' => 14800.00,
            'line_total' => 14800.00,
        ]);

        // INV 4: QuickCab Fleet Services (Partially Paid - 32,000 total, 12,000 paid, 20,000 due) -> Stage 6 Bulk Payment Pool
        $inv4 = Invoice::create([
            'invoice_number' => 'INV-202610-0004',
            'branch_id' => $branchColombo->id,
            'customer_id' => $customers[8]->id, // QuickCab
            'invoice_type' => 'service',
            'invoice_date' => Carbon::now()->subDays(4)->toDateString(),
            'subtotal' => 32000.00,
            'grand_total' => 32000.00,
            'amount_paid' => 12000.00,
            'balance_amount' => 20000.00,
            'status' => 'partially_paid',
            'created_by' => $admin->id,
        ]);
        InvoiceItem::create([
            'invoice_id' => $inv4->id,
            'item_type' => 'service',
            'item_name' => 'Corporate Fleet Multi-Van Brake Overhaul',
            'quantity' => 1,
            'unit_price' => 32000.00,
            'line_total' => 32000.00,
        ]);
        Payment::create([
            'invoice_id' => $inv4->id,
            'customer_id' => $inv4->customer_id,
            'payment_number' => 'PAY-202610-0004',
            'payment_date' => $inv4->invoice_date,
            'amount' => 12000.00,
            'payment_method' => 'bank_transfer',
            'reference_number' => 'TXN-QC-PARTIAL',
            'status' => 'completed',
            'created_by' => $admin->id,
        ]);

        // INV 5: Direct Sale DS-0005 (Paid 18,000 LKR) -> Stage 6 Avoid Direct Sale Target!
        $ds5 = $directSales['DS-202610-0005'];
        $inv5 = Invoice::create([
            'invoice_number' => 'INV-202610-0005',
            'branch_id' => $branchColombo->id,
            'customer_id' => $customers[0]->id, // Sunil Shantha
            'invoice_type' => 'direct_sale',
            'invoice_date' => Carbon::now()->subDays(2)->toDateString(),
            'subtotal' => $ds5->grand_total,
            'grand_total' => $ds5->grand_total,
            'amount_paid' => $ds5->grand_total,
            'balance_amount' => 0.00,
            'status' => 'paid',
            'created_by' => $admin->id,
        ]);
        $ds5->update(['invoice_id' => $inv5->id]);
        Payment::create([
            'invoice_id' => $inv5->id,
            'customer_id' => $inv5->customer_id,
            'payment_number' => 'PAY-202610-0005',
            'payment_date' => $inv5->invoice_date,
            'amount' => $inv5->grand_total,
            'payment_method' => 'card',
            'reference_number' => 'CARD-DS-0005',
            'status' => 'completed',
            'created_by' => $admin->id,
        ]);

        // INV 6: Job Card JOB-10 (Paid 10,400 LKR) -> Stage 6 Avoid Service Sale Target!
        $job10 = $jobCards['JOB-202610-0010'];
        $inv6 = Invoice::create([
            'invoice_number' => 'INV-202610-0006',
            'branch_id' => $branchColombo->id,
            'customer_id' => $customers[0]->id, // Sunil Shantha
            'invoice_type' => 'service',
            'invoice_date' => Carbon::now()->subDays(2)->toDateString(),
            'subtotal' => $job10->grand_total,
            'grand_total' => $job10->grand_total,
            'amount_paid' => $job10->grand_total,
            'balance_amount' => 0.00,
            'status' => 'paid',
            'created_by' => $admin->id,
        ]);
        $job10->update(['invoice_id' => $inv6->id]);
        Payment::create([
            'invoice_id' => $inv6->id,
            'customer_id' => $inv6->customer_id,
            'payment_number' => 'PAY-202610-0006',
            'payment_date' => $inv6->invoice_date,
            'amount' => $inv6->grand_total,
            'payment_method' => 'card',
            'reference_number' => 'CARD-JOB-0010',
            'status' => 'completed',
            'created_by' => $admin->id,
        ]);

        // INV 7: Nimal Jayasinghe (Unpaid 12,500 LKR, Issued 5 days ago) -> Stage 6 Invoice Date Change Target!
        $inv7 = Invoice::create([
            'invoice_number' => 'INV-202610-0007',
            'branch_id' => $branchKandy->id,
            'customer_id' => $customers[1]->id, // Nimal Jayasinghe
            'invoice_type' => 'service',
            'invoice_date' => Carbon::now()->subDays(5)->toDateString(),
            'subtotal' => 12500.00,
            'grand_total' => 12500.00,
            'amount_paid' => 0.00,
            'balance_amount' => 12500.00,
            'status' => 'issued',
            'created_by' => $admin->id,
        ]);
        InvoiceItem::create([
            'invoice_id' => $inv7->id,
            'item_type' => 'service',
            'item_name' => 'Brake Master Cylinder & Fluid Flush',
            'quantity' => 1,
            'unit_price' => 12500.00,
            'line_total' => 12500.00,
        ]);

        // INV 8: Kasun Bandara (Unpaid 25,000 LKR) -> Stage 6 Split Payment Target!
        $inv8 = Invoice::create([
            'invoice_number' => 'INV-202610-0008',
            'branch_id' => $branchColombo->id,
            'customer_id' => $customers[2]->id, // Kasun Bandara
            'invoice_type' => 'service',
            'invoice_date' => Carbon::now()->subDays(1)->toDateString(),
            'subtotal' => 25000.00,
            'grand_total' => 25000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 25000.00,
            'status' => 'issued',
            'created_by' => $admin->id,
        ]);
        InvoiceItem::create([
            'invoice_id' => $inv8->id,
            'item_type' => 'service',
            'item_name' => 'SUV 4WD Transfer Case & Suspension Service',
            'quantity' => 1,
            'unit_price' => 25000.00,
            'line_total' => 25000.00,
        ]);

        // =========================================================================
        // 7. EMPLOYEES & PAYROLL (6 Employees, Attendance, Advances, Paid & Draft Payroll)
        // =========================================================================
        $employeesData = [
            ['num' => 'EMP-0001', 'name' => 'Nilantha Kumara',   'mobile' => '0771234567', 'wage' => 3500.00, 'position' => 'Senior Mechanic',          'join' => '2026-01-10', 'addr' => 'No. 45, Kandy Road, Kelaniya'],
            ['num' => 'EMP-0002', 'name' => 'Roshan Silva',       'mobile' => '0754567890', 'wage' => 3200.00, 'position' => 'Auto Electrician',         'join' => '2026-02-15', 'addr' => 'No. 88, Galle Road, Dehiwala'],
            ['num' => 'EMP-0003', 'name' => 'Iresha Perera',      'mobile' => '0719876543', 'wage' => 2500.00, 'position' => 'Detailing Specialist',     'join' => '2026-03-01', 'addr' => 'No. 12, Station Road, Ragama'],
            ['num' => 'EMP-0004', 'name' => 'Hasini Kumari',      'mobile' => '0761122334', 'wage' => 2800.00, 'position' => 'Service Coordinator',     'join' => '2026-04-01', 'addr' => 'No. 7, Temple Road, Maharagama'],
            ['num' => 'EMP-0005', 'name' => 'Tharindu Jayakody',  'mobile' => '0783344556', 'wage' => 2200.00, 'position' => 'Junior Technician',        'join' => '2026-05-15', 'addr' => 'No. 55, High Level Road, Nugegoda'],
            ['num' => 'EMP-0006', 'name' => 'Dinesh Madushanka', 'mobile' => '0729988771', 'wage' => 1800.00, 'position' => 'Shop Helper & Lubricator', 'join' => '2026-06-01', 'addr' => 'No. 90, Baseline Road, Dematagoda'],
        ];

        $employees = [];
        foreach ($employeesData as $ed) {
            $emp = Employee::create([
                'employee_number' => $ed['num'],
                'branch_id' => $branchColombo->id,
                'name' => $ed['name'],
                'mobile' => $ed['mobile'],
                'address' => $ed['addr'],
                'position' => $ed['position'],
                'daily_wage' => $ed['wage'],
                'joining_date' => $ed['join'],
                'is_active' => true,
            ]);

            EmployeeWageRate::create([
                'employee_id' => $emp->id,
                'daily_wage' => $ed['wage'],
                'effective_from' => $ed['join'],
                'reason' => 'Initial approved daily wage rate.',
            ]);

            $employees[$ed['num']] = $emp;
        }

        // Attendance for September 2026 (Month 9) & October 2026 (Month 10)
        // Seed 20 working days in September
        foreach ($employees as $emp) {
            for ($day = 1; $day <= 25; $day++) {
                $attDate = Carbon::create(2026, 9, $day);
                if ($attDate->isSunday()) {
                    continue;
                }

                $status = ($day % 12 === 0) ? 'leave' : (($day % 7 === 0) ? 'half_day' : 'full_day');
                $fraction = $status === 'full_day' ? 1.0 : ($status === 'half_day' ? 0.5 : 0.0);

                EmployeeAttendance::create([
                    'employee_id' => $emp->id,
                    'attendance_date' => $attDate->toDateString(),
                    'status' => $status,
                    'payable_day_fraction' => $fraction,
                    'applied_daily_wage' => $emp->daily_wage,
                    'daily_earnings' => $fraction * (float) $emp->daily_wage,
                    'recorded_by' => $admin->id,
                ]);
            }

            // October attendance (first 18 days)
            for ($day = 1; $day <= 18; $day++) {
                $attDate = Carbon::create(2026, 10, $day);
                if ($attDate->isSunday()) {
                    continue;
                }

                $status = ($day % 9 === 0) ? 'absent' : (($day % 5 === 0) ? 'half_day' : 'full_day');
                $fraction = $status === 'full_day' ? 1.0 : ($status === 'half_day' ? 0.5 : 0.0);

                EmployeeAttendance::create([
                    'employee_id' => $emp->id,
                    'attendance_date' => $attDate->toDateString(),
                    'status' => $status,
                    'payable_day_fraction' => $fraction,
                    'applied_daily_wage' => $emp->daily_wage,
                    'daily_earnings' => $fraction * (float) $emp->daily_wage,
                    'recorded_by' => $admin->id,
                ]);
            }
        }

        // Salary Advances
        $adv1 = EmployeeAdvance::create([
            'advance_number' => 'ADV-0001',
            'employee_id' => $employees['EMP-0001']->id,
            'advance_date' => '2026-09-15',
            'amount' => 10000.00,
            'payment_method' => 'cash',
            'status' => 'deducted',
            'created_by' => $admin->id,
        ]);

        $adv2 = EmployeeAdvance::create([
            'advance_number' => 'ADV-0002',
            'employee_id' => $employees['EMP-0002']->id,
            'advance_date' => '2026-10-10',
            'amount' => 5000.00,
            'payment_method' => 'cash',
            'status' => 'pending',
            'created_by' => $admin->id,
        ]);

        // September 2026 Approved & Paid Payroll Period
        $septPayroll = PayrollPeriod::create([
            'year' => 2026,
            'month' => 9,
            'status' => 'paid',
            'total_base_earnings' => 380000.00,
            'total_gross' => 380000.00,
            'total_advances' => 10000.00,
            'total_net' => 370000.00,
            'total_paid' => 370000.00,
            'approved_by' => $admin->id,
            'approved_at' => '2026-09-30 18:00:00',
        ]);

        foreach ($employees as $emp) {
            $base = (float) $emp->daily_wage * 21.5;
            $advanceDed = $emp->id === $employees['EMP-0001']->id ? 10000.00 : 0.00;
            $net = $base - $advanceDed;

            $item = PayrollItem::create([
                'payroll_period_id' => $septPayroll->id,
                'employee_id' => $emp->id,
                'employee_name_snapshot' => $emp->name,
                'daily_wage_snapshot' => $emp->daily_wage,
                'payable_days' => 21.5,
                'base_earnings' => $base,
                'gross_salary' => $base,
                'advances_applied' => $advanceDed,
                'net_salary' => $net,
                'paid_amount' => $net,
                'balance_due' => 0.00,
                'status' => 'paid',
            ]);

            if ($emp->id === $employees['EMP-0001']->id) {
                $adv1->update(['payroll_item_id' => $item->id]);
            }

            PayrollPayment::create([
                'payroll_item_id' => $item->id,
                'payment_number' => PayrollPayment::generateNextPaymentNumber(),
                'payment_date' => '2026-09-30',
                'amount' => $net,
                'payment_method' => 'bank_transfer',
                'reference_number' => 'SAL-202609-'.$emp->id,
                'recorded_by' => $admin->id,
            ]);
        }

        // October 2026 Draft Payroll Period (Ready for Manual Testing)
        $octPayroll = PayrollPeriod::create([
            'year' => 2026,
            'month' => 10,
            'status' => 'draft',
            'total_base_earnings' => 250000.00,
            'total_gross' => 250000.00,
            'total_advances' => 5000.00,
            'total_net' => 245000.00,
            'total_paid' => 0.00,
        ]);

        foreach ($employees as $emp) {
            $base = (float) $emp->daily_wage * 14.5;
            $advanceDed = $emp->id === $employees['EMP-0002']->id ? 5000.00 : 0.00;
            $net = $base - $advanceDed;

            PayrollItem::create([
                'payroll_period_id' => $octPayroll->id,
                'employee_id' => $emp->id,
                'employee_name_snapshot' => $emp->name,
                'daily_wage_snapshot' => $emp->daily_wage,
                'payable_days' => 14.5,
                'base_earnings' => $base,
                'gross_salary' => $base,
                'advances_applied' => $advanceDed,
                'net_salary' => $net,
                'paid_amount' => 0.00,
                'balance_due' => $net,
                'status' => 'draft',
            ]);
        }

        // =========================================================================
        // 8. EXPENSES (Default Categories & 15 Expense Records: Staff + Shop)
        // =========================================================================
        ExpenseCategory::seedDefaults();
        $categories = ExpenseCategory::all();

        $expensesData = [
            ['type' => 'shop',  'cat' => 'Maintenance',                      'amt' => 75000.00, 'date' => '2026-09-05', 'method' => 'bank_transfer', 'desc' => 'Colombo Workshop Main Building Monthly Rental'],
            ['type' => 'shop',  'cat' => 'Electricity',                      'amt' => 38500.00, 'date' => '2026-09-12', 'method' => 'bank_transfer', 'desc' => 'CEB 3-Phase Commercial Electricity Bill Payment'],
            ['type' => 'shop',  'cat' => 'Chemicals',                        'amt' => 14200.00, 'date' => '2026-09-18', 'method' => 'cash',          'desc' => 'Air compressor hoses, connectors and solvent cleaner'],
            ['type' => 'shop',  'cat' => 'Staff Welfare',                    'amt' => 5800.00,  'date' => '2026-09-22', 'method' => 'cash',          'desc' => 'Waiting lounge tea, coffee and mineral water cans'],
            ['type' => 'shop',  'cat' => 'Repair',                           'amt' => 22000.00, 'date' => '2026-09-28', 'method' => 'cheque',        'desc' => 'Laser 4-wheel alignment machine sensor re-calibration'],
            ['type' => 'staff', 'cat' => 'Salary Advance',                   'amt' => 10000.00, 'date' => '2026-09-15', 'method' => 'cash',          'desc' => 'Mid-month salary advance for Nilantha Kumara', 'emp' => $employees['EMP-0001'], 'adv' => $adv1],
            ['type' => 'shop',  'cat' => 'Other',                            'amt' => 6400.00,  'date' => '2026-09-29', 'method' => 'cash',          'desc' => 'A4 job card printing paper, invoice envelopes and ink'],
            ['type' => 'shop',  'cat' => 'Other',                            'amt' => 8500.00,  'date' => '2026-10-02', 'method' => 'cash',          'desc' => 'Fuel reimbursement for picking up urgent filters from Panchikawatta'],
            ['type' => 'shop',  'cat' => 'Maintenance',                      'amt' => 75000.00, 'date' => '2026-10-05', 'method' => 'bank_transfer', 'desc' => 'Colombo Workshop October Rental Payment'],
            ['type' => 'shop',  'cat' => 'Water',                            'amt' => 41200.00, 'date' => '2026-10-08', 'method' => 'bank_transfer', 'desc' => 'October Water Supply & Municipal Drainage Charges'],
            ['type' => 'staff', 'cat' => 'Salary Advance',                   'amt' => 5000.00,  'date' => '2026-10-10', 'method' => 'cash',          'desc' => 'Medical advance for Roshan Silva', 'emp' => $employees['EMP-0002'], 'adv' => $adv2],
            ['type' => 'shop',  'cat' => 'Chemicals',                        'amt' => 11500.00, 'date' => '2026-10-12', 'method' => 'cash',          'desc' => 'Safety gloves, cotton waste rags and degreaser cans'],
            ['type' => 'shop',  'cat' => 'Staff Welfare',                    'amt' => 6200.00,  'date' => '2026-10-14', 'method' => 'cash',          'desc' => 'Customer lounge snacks and air freshener refills'],
            ['type' => 'shop',  'cat' => 'Other',                            'amt' => 9800.00,  'date' => '2026-10-16', 'method' => 'cash',          'desc' => 'Company recovery truck diesel fuel'],
            ['type' => 'shop',  'cat' => 'Other',                            'amt' => 4500.00,  'date' => '2026-10-18', 'method' => 'cash',          'desc' => 'Workshop first aid box medicine restock'],
        ];

        foreach ($expensesData as $ex) {
            $cat = $categories->firstWhere('name', $ex['cat']) ?? $categories->first();
            $emp = $ex['emp'] ?? null;
            $adv = $ex['adv'] ?? null;

            Expense::create([
                'expense_number' => Expense::generateNextExpenseNumber(),
                'branch_id' => $branchColombo->id,
                'category_id' => $cat->id,
                'expense_type' => $ex['type'],
                'employee_id' => $emp?->id,
                'employee_advance_id' => $adv?->id,
                'expense_date' => $ex['date'],
                'related_month' => substr($ex['date'], 0, 7),
                'description' => $ex['desc'],
                'amount' => $ex['amt'],
                'payment_method' => $ex['method'],
                'payment_date' => $ex['date'],
                'status' => 'active',
                'created_by' => $admin->id,
            ]);
        }
    }
}
