<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\CustomerCreditAccount;
use App\Models\DirectSale;
use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\Invoice;
use App\Models\JobCard;
use App\Models\JobCardService;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\PaymentReceipt;
use App\Models\PayrollPeriod;
use App\Models\Product;
use App\Models\Service;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\UnitOfMeasurement;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;
use RuntimeException;

class Stage7ManualTestSeeder extends Seeder
{
    /**
     * Run the database seeds for Stage 7 manual testing.
     */
    public function run(): void
    {
        // 1. SAFETY ENVIRONMENT CHECK
        if (app()->environment('production')) {
            throw new RuntimeException('ABORTED: Stage7ManualTestSeeder cannot be executed in production environment.');
        }

        $admin = User::where('role', 'admin')->first() ?? User::firstOrFail();
        $staff = User::where('role', 'staff')->first() ?? $admin;

        // Ensure branches exist
        $branchColombo = Branch::where('is_main_branch', true)->first() ?? Branch::firstOrCreate(
            ['branch_code' => 'BR-001'],
            [
                'company_id' => 1,
                'branch_name' => 'AutoCare Colombo Main Branch',
                'is_main_branch' => true,
                'is_active' => true,
            ]
        );

        $branchKandy = Branch::where('branch_code', 'BR-002')->first() ?? Branch::firstOrCreate(
            ['branch_code' => 'BR-002'],
            [
                'company_id' => 1,
                'branch_name' => 'AutoCare Kandy Service Center',
                'is_main_branch' => false,
                'is_active' => true,
            ]
        );

        // 2. BACKFILL: Ensure existing batches/movements without branch_id are mapped to Main Branch
        StockBatch::whereNull('branch_id')->update(['branch_id' => $branchColombo->id]);
        StockMovement::whereNull('branch_id')->update(['branch_id' => $branchColombo->id]);
        GoodsReceivedNote::whereNull('branch_id')->update(['branch_id' => $branchColombo->id]);
        Invoice::whereNull('branch_id')->update(['branch_id' => $branchColombo->id]);
        JobCard::whereNull('branch_id')->update(['branch_id' => $branchColombo->id]);
        DirectSale::whereNull('branch_id')->update(['branch_id' => $branchColombo->id]);

        $supplier = Supplier::first() ?? Supplier::create([
            'supplier_name' => 'Denso Lanka (Pvt) Ltd',
            'contact_person' => 'Rohan Dias',
            'phone' => '0112334455',
            'email' => 'sales@densolanka.lk',
            'status' => 'active',
        ]);

        $uomItem = UnitOfMeasurement::where('code', 'ITM')->first() ?? UnitOfMeasurement::first();
        $uomBottle = UnitOfMeasurement::where('code', 'BTL')->first() ?? $uomItem;

        // Dedicated Test Product 1: Oil Filter (Stock Value Report FIFO Dual-Batch Test)
        $oilFilter = Product::where('sku', 'PRD-FLT-001')->first();
        if (! $oilFilter) {
            $oilFilter = Product::create([
                'sku' => 'PRD-FLT-001',
                'name' => 'Oil Filter',
                'uom_id' => $uomItem->id,
                'preferred_supplier_id' => $supplier->id,
                'min_stock' => 5,
                'max_stock' => 50,
                'selling_price' => 1800.00,
                'is_active' => true,
            ]);
        }

        // Dedicated Test Product 2: Engine Oil 1L (Stock Report Dual-Batch 35 Units Test)
        $engineOil1L = Product::where('sku', 'PRD-OIL-1L')->first();
        if (! $engineOil1L) {
            $engineOil1L = Product::create([
                'sku' => 'PRD-OIL-1L',
                'name' => 'Engine Oil 1L',
                'uom_id' => $uomBottle->id,
                'preferred_supplier_id' => $supplier->id,
                'min_stock' => 10,
                'max_stock' => 100,
                'selling_price' => 2200.00,
                'is_active' => true,
            ]);
        }

        // =========================================================================
        // 3. STOCK & STOCK VALUATION BATCHES
        // =========================================================================
        // GRN 1: Oil Filter Batch B001 (10 remaining @ 800 LKR = 8,000 LKR)
        $grnFilter1 = GoodsReceivedNote::updateOrCreate(
            ['grn_number' => 'GRN-202609-FLT01'],
            [
                'branch_id' => $branchColombo->id,
                'supplier_id' => $supplier->id,
                'supplier_invoice_number' => 'INV-SUP-FLT01',
                'received_date' => '2026-09-01',
                'status' => 'confirmed',
                'total_amount' => 12000.00,
                'posted_at' => '2026-09-01 10:00:00',
                'posted_by' => $admin->id,
                'created_by' => $admin->id,
            ]
        );

        $grnItemFilter1 = GoodsReceivedNoteItem::updateOrCreate(
            ['grn_id' => $grnFilter1->id, 'product_id' => $oilFilter->id],
            [
                'purchase_quantity' => 15,
                'free_quantity' => 0,
                'purchase_unit_price' => 800.00,
                'selling_price_snapshot' => 1800.00,
                'line_total' => 12000.00,
            ]
        );

        $batchFilter1 = StockBatch::updateOrCreate(
            ['batch_number' => 'B001'],
            [
                'branch_id' => $branchColombo->id,
                'grn_id' => $grnFilter1->id,
                'grn_item_id' => $grnItemFilter1->id,
                'product_id' => $oilFilter->id,
                'supplier_id' => $supplier->id,
                'purchase_date' => '2026-09-01',
                'received_quantity' => 15,
                'free_quantity' => 0,
                'remaining_quantity' => 10,
                'original_purchase_unit_price' => 800.00,
                'inventory_unit_cost' => 800.00,
                'selling_price_at_receipt' => 1800.00,
                'status' => 'active',
            ]
        );

        StockMovement::updateOrCreate(
            [
                'product_id' => $oilFilter->id,
                'stock_batch_id' => $batchFilter1->id,
                'movement_type' => 'grn_receipt',
            ],
            [
                'branch_id' => $branchColombo->id,
                'quantity' => 15,
                'unit_cost' => 800.00,
                'reference_type' => GoodsReceivedNote::class,
                'reference_id' => $grnFilter1->id,
                'notes' => 'Test Batch B001 receipt (Oil Filter)',
                'created_by' => $admin->id,
            ]
        );

        // GRN 2: Oil Filter Batch B002 (5 remaining @ 1,000 LKR = 5,000 LKR)
        // Expected Stock Value: 10 * 800 + 5 * 1000 = 13,000 LKR!
        $grnFilter2 = GoodsReceivedNote::updateOrCreate(
            ['grn_number' => 'GRN-202610-FLT02'],
            [
                'branch_id' => $branchColombo->id,
                'supplier_id' => $supplier->id,
                'supplier_invoice_number' => 'INV-SUP-FLT02',
                'received_date' => '2026-10-01',
                'status' => 'confirmed',
                'total_amount' => 8000.00,
                'posted_at' => '2026-10-01 10:00:00',
                'posted_by' => $admin->id,
                'created_by' => $admin->id,
            ]
        );

        $grnItemFilter2 = GoodsReceivedNoteItem::updateOrCreate(
            ['grn_id' => $grnFilter2->id, 'product_id' => $oilFilter->id],
            [
                'purchase_quantity' => 8,
                'free_quantity' => 0,
                'purchase_unit_price' => 1000.00,
                'selling_price_snapshot' => 1800.00,
                'line_total' => 8000.00,
            ]
        );

        $batchFilter2 = StockBatch::updateOrCreate(
            ['batch_number' => 'B002'],
            [
                'branch_id' => $branchColombo->id,
                'grn_id' => $grnFilter2->id,
                'grn_item_id' => $grnItemFilter2->id,
                'product_id' => $oilFilter->id,
                'supplier_id' => $supplier->id,
                'purchase_date' => '2026-10-01',
                'received_quantity' => 8,
                'free_quantity' => 0,
                'remaining_quantity' => 5,
                'original_purchase_unit_price' => 1000.00,
                'inventory_unit_cost' => 1000.00,
                'selling_price_at_receipt' => 1800.00,
                'status' => 'active',
            ]
        );

        StockMovement::updateOrCreate(
            [
                'product_id' => $oilFilter->id,
                'stock_batch_id' => $batchFilter2->id,
                'movement_type' => 'grn_receipt',
            ],
            [
                'branch_id' => $branchColombo->id,
                'quantity' => 8,
                'unit_cost' => 1000.00,
                'reference_type' => GoodsReceivedNote::class,
                'reference_id' => $grnFilter2->id,
                'notes' => 'Test Batch B002 receipt (Oil Filter)',
                'created_by' => $admin->id,
            ]
        );

        // GRN 3: Engine Oil 1L Batch 001 (Purchase 20, Sold 5, Remaining 15)
        $grnOil1 = GoodsReceivedNote::updateOrCreate(
            ['grn_number' => 'GRN-202609-OIL01'],
            [
                'branch_id' => $branchColombo->id,
                'supplier_id' => $supplier->id,
                'supplier_invoice_number' => 'INV-SUP-OIL01',
                'received_date' => '2026-09-02',
                'status' => 'confirmed',
                'total_amount' => 30000.00,
                'posted_at' => '2026-09-02 10:00:00',
                'posted_by' => $admin->id,
                'created_by' => $admin->id,
            ]
        );

        $grnItemOil1 = GoodsReceivedNoteItem::updateOrCreate(
            ['grn_id' => $grnOil1->id, 'product_id' => $engineOil1L->id],
            [
                'purchase_quantity' => 20,
                'free_quantity' => 0,
                'purchase_unit_price' => 1500.00,
                'selling_price_snapshot' => 2200.00,
                'line_total' => 30000.00,
            ]
        );

        $batchOil1 = StockBatch::updateOrCreate(
            ['batch_number' => 'BATCH-001'],
            [
                'branch_id' => $branchColombo->id,
                'grn_id' => $grnOil1->id,
                'grn_item_id' => $grnItemOil1->id,
                'product_id' => $engineOil1L->id,
                'supplier_id' => $supplier->id,
                'purchase_date' => '2026-09-02',
                'received_quantity' => 20,
                'free_quantity' => 0,
                'remaining_quantity' => 15,
                'original_purchase_unit_price' => 1500.00,
                'inventory_unit_cost' => 1500.00,
                'selling_price_at_receipt' => 2200.00,
                'status' => 'active',
            ]
        );

        StockMovement::updateOrCreate(
            [
                'product_id' => $engineOil1L->id,
                'stock_batch_id' => $batchOil1->id,
                'movement_type' => 'grn_receipt',
            ],
            [
                'branch_id' => $branchColombo->id,
                'quantity' => 20,
                'unit_cost' => 1500.00,
                'reference_type' => GoodsReceivedNote::class,
                'reference_id' => $grnOil1->id,
                'notes' => 'Test Batch 001 receipt (Engine Oil 1L)',
                'created_by' => $admin->id,
            ]
        );

        // GRN 4: Engine Oil 1L Batch 002 (Purchase 30, Sold 10, Remaining 20)
        // Total Available: 15 + 20 = 35 units!
        $grnOil2 = GoodsReceivedNote::updateOrCreate(
            ['grn_number' => 'GRN-202610-OIL02'],
            [
                'branch_id' => $branchColombo->id,
                'supplier_id' => $supplier->id,
                'supplier_invoice_number' => 'INV-SUP-OIL02',
                'received_date' => '2026-10-02',
                'status' => 'confirmed',
                'total_amount' => 48000.00,
                'posted_at' => '2026-10-02 10:00:00',
                'posted_by' => $admin->id,
                'created_by' => $admin->id,
            ]
        );

        $grnItemOil2 = GoodsReceivedNoteItem::updateOrCreate(
            ['grn_id' => $grnOil2->id, 'product_id' => $engineOil1L->id],
            [
                'purchase_quantity' => 30,
                'free_quantity' => 0,
                'purchase_unit_price' => 1600.00,
                'selling_price_snapshot' => 2200.00,
                'line_total' => 48000.00,
            ]
        );

        $batchOil2 = StockBatch::updateOrCreate(
            ['batch_number' => 'BATCH-002'],
            [
                'branch_id' => $branchColombo->id,
                'grn_id' => $grnOil2->id,
                'grn_item_id' => $grnItemOil2->id,
                'product_id' => $engineOil1L->id,
                'supplier_id' => $supplier->id,
                'purchase_date' => '2026-10-02',
                'received_quantity' => 30,
                'free_quantity' => 0,
                'remaining_quantity' => 20,
                'original_purchase_unit_price' => 1600.00,
                'inventory_unit_cost' => 1600.00,
                'selling_price_at_receipt' => 2200.00,
                'status' => 'active',
            ]
        );

        StockMovement::updateOrCreate(
            [
                'product_id' => $engineOil1L->id,
                'stock_batch_id' => $batchOil2->id,
                'movement_type' => 'grn_receipt',
            ],
            [
                'branch_id' => $branchColombo->id,
                'quantity' => 30,
                'unit_cost' => 1600.00,
                'reference_type' => GoodsReceivedNote::class,
                'reference_id' => $grnOil2->id,
                'notes' => 'Test Batch 002 receipt (Engine Oil 1L)',
                'created_by' => $admin->id,
            ]
        );

        // Kandy Branch Batch: Ferodo Ceramic Brake Pads (10 units @ 6,500 LKR = 65,000 LKR)
        $brakePad = Product::where('sku', 'PRD-FER-005')->first();
        if ($brakePad) {
            $grnKandy = GoodsReceivedNote::updateOrCreate(
                ['grn_number' => 'GRN-202610-KND01'],
                [
                    'branch_id' => $branchKandy->id,
                    'supplier_id' => $supplier->id,
                    'supplier_invoice_number' => 'INV-FER-KND1',
                    'received_date' => '2026-10-03',
                    'status' => 'confirmed',
                    'total_amount' => 65000.00,
                    'posted_at' => '2026-10-03 10:00:00',
                    'posted_by' => $admin->id,
                    'created_by' => $admin->id,
                ]
            );

            $grnItemKandy = GoodsReceivedNoteItem::updateOrCreate(
                ['grn_id' => $grnKandy->id, 'product_id' => $brakePad->id],
                [
                    'purchase_quantity' => 10,
                    'free_quantity' => 0,
                    'purchase_unit_price' => 6500.00,
                    'selling_price_snapshot' => 9500.00,
                    'line_total' => 65000.00,
                ]
            );

            $batchKandy = StockBatch::updateOrCreate(
                ['batch_number' => 'B26-10-KND01'],
                [
                    'branch_id' => $branchKandy->id,
                    'grn_id' => $grnKandy->id,
                    'grn_item_id' => $grnItemKandy->id,
                    'product_id' => $brakePad->id,
                    'supplier_id' => $supplier->id,
                    'purchase_date' => '2026-10-03',
                    'received_quantity' => 10,
                    'free_quantity' => 0,
                    'remaining_quantity' => 10,
                    'original_purchase_unit_price' => 6500.00,
                    'inventory_unit_cost' => 6500.00,
                    'selling_price_at_receipt' => 9500.00,
                    'status' => 'active',
                ]
            );

            StockMovement::updateOrCreate(
                [
                    'product_id' => $brakePad->id,
                    'stock_batch_id' => $batchKandy->id,
                    'movement_type' => 'grn_receipt',
                ],
                [
                    'branch_id' => $branchKandy->id,
                    'quantity' => 10,
                    'unit_cost' => 6500.00,
                    'reference_type' => GoodsReceivedNote::class,
                    'reference_id' => $grnKandy->id,
                    'notes' => 'Kandy Branch Test Batch B26-10-KND01 receipt',
                    'created_by' => $admin->id,
                ]
            );
        }

        // =========================================================================
        // 4. CUSTOMERS & CREDIT ACCOUNTS
        // =========================================================================
        // 4.1 Company 1: ABC Transport Ltd
        $customerAbc = Customer::updateOrCreate(
            ['phone' => '0112991122'],
            [
                'customer_name' => 'Sunil Fernando',
                'customer_type' => 'company',
                'company_name' => 'ABC Transport Ltd',
                'email' => 'fleet@abctransport.lk',
                'registration_number' => 'PV-ABC-1234',
                'tax_number' => 'VAT-ABC-9988',
                'address' => 'No. 88, Kandy Road, Peliyagoda',
                'status' => 'active',
            ]
        );

        CustomerCreditAccount::updateOrCreate(
            ['customer_id' => $customerAbc->id],
            [
                'credit_limit' => 50000.00,
                'opening_balance' => 0.00,
                'payment_terms_days' => 30,
                'status' => 'active',
                'notes' => 'Corporate fleet maintenance credit account - 30 days credit terms.',
                'created_by' => $admin->id,
            ]
        );

        // Vehicles for ABC Transport
        $typeVan = VehicleType::where('name', 'Van')->first() ?? VehicleType::first();
        $typeCar = VehicleType::where('name', 'Car')->first() ?? VehicleType::first();
        $typeLorry = VehicleType::where('name', 'Lorry')->first() ?? VehicleType::first();

        $vehCab = Vehicle::updateOrCreate(
            ['vehicle_number' => 'CAB-1234'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerAbc->id,
                'vehicle_type_id' => $typeVan->id,
                'make_model' => 'Toyota HiAce Super GL KDH201',
                'current_mileage' => 55000,
                'next_service_mileage' => 60000,
            ]
        );

        $vehCaa = Vehicle::updateOrCreate(
            ['vehicle_number' => 'CAA-5678'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerAbc->id,
                'vehicle_type_id' => $typeCar->id,
                'make_model' => 'Toyota Corolla Axio 1.5L',
                'current_mileage' => 32000,
                'next_service_mileage' => 37000,
            ]
        );

        $vehBcd = Vehicle::updateOrCreate(
            ['vehicle_number' => 'BCD-7890'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerAbc->id,
                'vehicle_type_id' => $typeLorry->id,
                'make_model' => 'Isuzu ELF 14.5ft Flatbed',
                'current_mileage' => 78000,
                'next_service_mileage' => 83000,
            ]
        );

        // 4.2 Company 2: XYZ Logistics Ltd
        $customerXyz = Customer::updateOrCreate(
            ['phone' => '0112883344'],
            [
                'customer_name' => 'Dinesh Gamage',
                'customer_type' => 'company',
                'company_name' => 'XYZ Logistics Ltd',
                'email' => 'accounts@xyzlogistics.lk',
                'registration_number' => 'PV-XYZ-5678',
                'tax_number' => 'VAT-XYZ-5544',
                'address' => 'No. 150, Baseline Road, Colombo 09',
                'status' => 'active',
            ]
        );

        CustomerCreditAccount::updateOrCreate(
            ['customer_id' => $customerXyz->id],
            [
                'credit_limit' => 100000.00,
                'opening_balance' => 0.00,
                'payment_terms_days' => 30,
                'status' => 'active',
                'notes' => 'Heavy transport logistics fleet credit account.',
                'created_by' => $admin->id,
            ]
        );

        $vehXyz1 = Vehicle::updateOrCreate(
            ['vehicle_number' => 'WP-NB-1122'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerXyz->id,
                'vehicle_type_id' => $typeLorry->id,
                'make_model' => 'Mitsubishi Fuso Canter 16ft',
                'current_mileage' => 110000,
                'next_service_mileage' => 115000,
            ]
        );

        $vehXyz2 = Vehicle::updateOrCreate(
            ['vehicle_number' => 'WP-NB-3344'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerXyz->id,
                'vehicle_type_id' => $typeLorry->id,
                'make_model' => 'Isuzu Forward 20ft Cargo Carrier',
                'current_mileage' => 145000,
                'next_service_mileage' => 150000,
            ]
        );

        // 4.3 Individual Customer: Kamal Gunaratne
        $customerKamal = Customer::updateOrCreate(
            ['phone' => '0779988776'],
            [
                'customer_name' => 'Kamal Gunaratne',
                'customer_type' => 'individual',
                'email' => 'kamal.gunaratne@gmail.com',
                'address' => 'No. 22, Station Road, Nugegoda',
                'status' => 'active',
            ]
        );

        CustomerCreditAccount::updateOrCreate(
            ['customer_id' => $customerKamal->id],
            [
                'credit_limit' => 40000.00,
                'opening_balance' => 0.00,
                'payment_terms_days' => 14,
                'status' => 'active',
                'notes' => 'Individual executive account approved for 14-day credit.',
                'created_by' => $admin->id,
            ]
        );

        $vehKamal = Vehicle::updateOrCreate(
            ['vehicle_number' => 'WP-CAE-4554'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerKamal->id,
                'vehicle_type_id' => $typeCar->id,
                'make_model' => 'Toyota Allion NZT260 1.5L',
                'current_mileage' => 42000,
                'next_service_mileage' => 47000,
            ]
        );

        // 4.4 Company 3: Lanka Cabs & Tours (Pvt) Ltd (For Credit Aging Verification Across All Intervals)
        $customerLanka = Customer::updateOrCreate(
            ['phone' => '0112778899'],
            [
                'customer_name' => 'Gamini Wijesinghe',
                'customer_type' => 'company',
                'company_name' => 'Lanka Cabs & Tours (Pvt) Ltd',
                'email' => 'accounts@lankacabs.lk',
                'registration_number' => 'PV-LKA-9988',
                'tax_number' => 'VAT-LKA-3322',
                'address' => 'No. 45, Galle Road, Colombo 03',
                'status' => 'active',
            ]
        );

        CustomerCreditAccount::updateOrCreate(
            ['customer_id' => $customerLanka->id],
            [
                'credit_limit' => 200000.00,
                'opening_balance' => 0.00,
                'payment_terms_days' => 30,
                'status' => 'active',
                'notes' => 'Tour fleet credit account for testing credit aging buckets.',
                'created_by' => $admin->id,
            ]
        );

        $vehLanka1 = Vehicle::updateOrCreate(
            ['vehicle_number' => 'WP-CAB-8811'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerLanka->id,
                'vehicle_type_id' => $typeVan->id,
                'make_model' => 'Toyota HiAce GL Tour Van',
                'current_mileage' => 88000,
                'next_service_mileage' => 93000,
            ]
        );

        // 4.5 Vehicles with NO Service History (Testing Unserviced / Blank Report Queries)
        $vehNoHistory1 = Vehicle::updateOrCreate(
            ['vehicle_number' => 'WP-NEW-1010'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerKamal->id,
                'vehicle_type_id' => $typeCar->id,
                'make_model' => 'Honda Grace Hybrid EX 1.5L',
                'current_mileage' => 15000,
                'next_service_mileage' => 20000,
            ]
        );

        $vehNoHistory2 = Vehicle::updateOrCreate(
            ['vehicle_number' => 'WP-NEW-2020'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerAbc->id,
                'vehicle_type_id' => $typeCar->id,
                'make_model' => 'Toyota Vitz F Safety Edition 1.0L',
                'current_mileage' => 28000,
                'next_service_mileage' => 33000,
            ]
        );

        $oilService = Service::first() ?? Service::create([
            'code' => 'SRV-OIL',
            'name' => 'Full Synthetic Engine Oil & Filter Change',
            'status' => 'active',
        ]);

        // =========================================================================
        // 5. VEHICLE SERVICE JOBS & INVOICES (September & October 2026)
        // =========================================================================

        // 5.1 September Historical Service for CAB-1234
        $jobSepCab = JobCard::updateOrCreate(
            ['job_number' => 'JOB-202609-0001'],
            [
                'branch_id' => $branchColombo->id,
                'date' => '2026-09-05',
                'customer_id' => $customerAbc->id,
                'vehicle_id' => $vehCab->id,
                'vehicle_type_id' => $typeVan->id,
                'current_mileage' => 50000,
                'next_service_mileage' => 55000,
                'status' => 'completed',
                'service_total' => 4500.00,
                'product_total' => 7500.00,
                'grand_total' => 12000.00,
                'created_by' => $admin->id,
            ]
        );

        JobCardService::updateOrCreate(
            ['job_card_id' => $jobSepCab->id, 'service_id' => $oilService->id],
            [
                'service_code' => $oilService->code,
                'service_name' => $oilService->name,
                'unit_price' => 4500.00,
                'quantity' => 1,
                'line_total' => 4500.00,
            ]
        );

        $invSepCab = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202609-0001'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerAbc->id,
                'job_card_id' => $jobSepCab->id,
                'invoice_date' => '2026-09-05',
                'due_date' => '2026-10-05',
                'invoice_type' => 'job_card',
                'subtotal' => 12000.00,
                'grand_total' => 12000.00,
                'amount_paid' => 12000.00,
                'balance_amount' => 0.00,
                'status' => 'paid',
                'payment_method' => 'bank_transfer',
                'created_by' => $admin->id,
            ]
        );
        $jobSepCab->update(['invoice_id' => $invSepCab->id]);

        Payment::updateOrCreate(
            ['payment_number' => 'PAY-202609-0001'],
            [
                'invoice_id' => $invSepCab->id,
                'customer_id' => $customerAbc->id,
                'payment_date' => '2026-09-05',
                'amount' => 12000.00,
                'payment_method' => 'bank_transfer',
                'reference_number' => 'TRX-BOC-554411',
                'status' => 'completed',
                'created_by' => $admin->id,
            ]
        );

        // 5.1b September Historical Service for CAA-5678 (2nd completed job for CAA-5678)
        $jobSepCaa = JobCard::updateOrCreate(
            ['job_number' => 'JOB-202609-0002'],
            [
                'branch_id' => $branchColombo->id,
                'date' => '2026-09-10',
                'customer_id' => $customerAbc->id,
                'vehicle_id' => $vehCaa->id,
                'vehicle_type_id' => $typeCar->id,
                'current_mileage' => 28000,
                'next_service_mileage' => 33000,
                'status' => 'completed',
                'service_total' => 3500.00,
                'product_total' => 4500.00,
                'grand_total' => 8000.00,
                'created_by' => $admin->id,
            ]
        );

        JobCardService::updateOrCreate(
            ['job_card_id' => $jobSepCaa->id, 'service_id' => $oilService->id],
            [
                'service_code' => $oilService->code,
                'service_name' => 'Periodic Brake Inspection & Engine Tune-up',
                'unit_price' => 3500.00,
                'quantity' => 1,
                'line_total' => 3500.00,
            ]
        );

        // 5.1c September Historical Service for WP-NB-1122 (2nd completed job for XYZ Logistics)
        $jobSepXyz1 = JobCard::updateOrCreate(
            ['job_number' => 'JOB-202609-0003'],
            [
                'branch_id' => $branchColombo->id,
                'date' => '2026-09-18',
                'customer_id' => $customerXyz->id,
                'vehicle_id' => $vehXyz1->id,
                'vehicle_type_id' => $typeLorry->id,
                'current_mileage' => 102000,
                'next_service_mileage' => 107000,
                'status' => 'completed',
                'service_total' => 10000.00,
                'product_total' => 15000.00,
                'grand_total' => 25000.00,
                'created_by' => $admin->id,
            ]
        );

        JobCardService::updateOrCreate(
            ['job_card_id' => $jobSepXyz1->id, 'service_id' => $oilService->id],
            [
                'service_code' => $oilService->code,
                'service_name' => 'Heavy Commercial Fleet Lubrication & Hub Greasing',
                'unit_price' => 10000.00,
                'quantity' => 1,
                'line_total' => 10000.00,
            ]
        );

        // 5.2 ABC Transport: October Services (Total = 35,000 LKR)
        // Service 1: CAB-1234 (15,000 LKR)
        $jobOct1 = JobCard::updateOrCreate(
            ['job_number' => 'JOB-202610-0010'],
            [
                'branch_id' => $branchColombo->id,
                'date' => '2026-10-05',
                'customer_id' => $customerAbc->id,
                'vehicle_id' => $vehCab->id,
                'vehicle_type_id' => $typeVan->id,
                'current_mileage' => 55000,
                'next_service_mileage' => 60000,
                'status' => 'completed',
                'service_total' => 5500.00,
                'product_total' => 9500.00,
                'grand_total' => 15000.00,
                'created_by' => $admin->id,
            ]
        );

        JobCardService::updateOrCreate(
            ['job_card_id' => $jobOct1->id, 'service_id' => $oilService->id],
            [
                'service_code' => $oilService->code,
                'service_name' => $oilService->name,
                'unit_price' => 5500.00,
                'quantity' => 1,
                'line_total' => 5500.00,
            ]
        );

        $invOct1 = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202610-0010'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerAbc->id,
                'job_card_id' => $jobOct1->id,
                'invoice_date' => '2026-10-05',
                'due_date' => '2026-11-04',
                'invoice_type' => 'job_card',
                'subtotal' => 15000.00,
                'grand_total' => 15000.00,
                'amount_paid' => 15000.00, // Fully settled by Oct 18 payment
                'balance_amount' => 0.00,
                'status' => 'paid',
                'payment_method' => 'bank_transfer',
                'created_by' => $admin->id,
            ]
        );
        $jobOct1->update(['invoice_id' => $invOct1->id]);

        // Service 2: CAA-5678 (8,000 LKR)
        $jobOct2 = JobCard::updateOrCreate(
            ['job_number' => 'JOB-202610-0011'],
            [
                'branch_id' => $branchColombo->id,
                'date' => '2026-10-10',
                'customer_id' => $customerAbc->id,
                'vehicle_id' => $vehCaa->id,
                'vehicle_type_id' => $typeCar->id,
                'current_mileage' => 32000,
                'next_service_mileage' => 37000,
                'status' => 'completed',
                'service_total' => 3500.00,
                'product_total' => 4500.00,
                'grand_total' => 8000.00,
                'created_by' => $admin->id,
            ]
        );

        JobCardService::updateOrCreate(
            ['job_card_id' => $jobOct2->id, 'service_id' => $oilService->id],
            [
                'service_code' => $oilService->code,
                'service_name' => $oilService->name,
                'unit_price' => 3500.00,
                'quantity' => 1,
                'line_total' => 3500.00,
            ]
        );

        $invOct2 = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202610-0011'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerAbc->id,
                'job_card_id' => $jobOct2->id,
                'invoice_date' => '2026-10-10',
                'due_date' => '2026-11-09',
                'invoice_type' => 'job_card',
                'subtotal' => 8000.00,
                'grand_total' => 8000.00,
                'amount_paid' => 5000.00, // Partially settled by Oct 18 payment
                'balance_amount' => 3000.00,
                'status' => 'partially_paid',
                'payment_method' => 'bank_transfer',
                'created_by' => $admin->id,
            ]
        );
        $jobOct2->update(['invoice_id' => $invOct2->id]);

        // Service 3: BCD-7890 (12,000 LKR) - Unpaid
        $jobOct3 = JobCard::updateOrCreate(
            ['job_number' => 'JOB-202610-0012'],
            [
                'branch_id' => $branchColombo->id,
                'date' => '2026-10-14',
                'customer_id' => $customerAbc->id,
                'vehicle_id' => $vehBcd->id,
                'vehicle_type_id' => $typeLorry->id,
                'current_mileage' => 78000,
                'next_service_mileage' => 83000,
                'status' => 'completed',
                'service_total' => 4000.00,
                'product_total' => 8000.00,
                'grand_total' => 12000.00,
                'created_by' => $admin->id,
            ]
        );

        JobCardService::updateOrCreate(
            ['job_card_id' => $jobOct3->id, 'service_id' => $oilService->id],
            [
                'service_code' => $oilService->code,
                'service_name' => $oilService->name,
                'unit_price' => 4000.00,
                'quantity' => 1,
                'line_total' => 4000.00,
            ]
        );

        $invOct3 = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202610-0012'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerAbc->id,
                'job_card_id' => $jobOct3->id,
                'invoice_date' => '2026-10-14',
                'due_date' => '2026-11-13',
                'invoice_type' => 'job_card',
                'subtotal' => 12000.00,
                'grand_total' => 12000.00,
                'amount_paid' => 0.00,
                'balance_amount' => 12000.00,
                'status' => 'issued',
                'payment_method' => 'credit',
                'created_by' => $admin->id,
            ]
        );
        $jobOct3->update(['invoice_id' => $invOct3->id]);

        // 5.3 ABC Transport Payment on 2026-10-18: 20,000 LKR
        $receiptAbc = PaymentReceipt::updateOrCreate(
            ['receipt_number' => 'REC-202610-0001'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerAbc->id,
                'receipt_date' => '2026-10-18',
                'total_amount' => 20000.00,
                'payment_method' => 'bank_transfer',
                'reference_number' => 'TRX-BOC-998822',
                'notes' => 'Fleet October partial settlement: INV-202610-0010 (15,000) & INV-202610-0011 (5,000).',
                'created_by' => $admin->id,
            ]
        );

        PaymentAllocation::updateOrCreate(
            ['payment_receipt_id' => $receiptAbc->id, 'invoice_id' => $invOct1->id],
            ['allocated_amount' => 15000.00]
        );
        PaymentAllocation::updateOrCreate(
            ['payment_receipt_id' => $receiptAbc->id, 'invoice_id' => $invOct2->id],
            ['allocated_amount' => 5000.00]
        );

        Payment::updateOrCreate(
            ['payment_number' => 'PAY-202610-0010'],
            [
                'invoice_id' => $invOct1->id,
                'customer_id' => $customerAbc->id,
                'payment_receipt_id' => $receiptAbc->id,
                'payment_date' => '2026-10-18',
                'amount' => 15000.00,
                'payment_method' => 'bank_transfer',
                'reference_number' => 'TRX-BOC-998822-1',
                'status' => 'completed',
                'created_by' => $admin->id,
            ]
        );

        Payment::updateOrCreate(
            ['payment_number' => 'PAY-202610-0011'],
            [
                'invoice_id' => $invOct2->id,
                'customer_id' => $customerAbc->id,
                'payment_receipt_id' => $receiptAbc->id,
                'payment_date' => '2026-10-18',
                'amount' => 5000.00,
                'payment_method' => 'bank_transfer',
                'reference_number' => 'TRX-BOC-998822-2',
                'status' => 'completed',
                'created_by' => $admin->id,
            ]
        );

        // 5.4 XYZ Logistics: 2 Unpaid Invoices Totaling 90,000 LKR (From 100,000 LKR Limit)
        $jobOctXyz1 = JobCard::updateOrCreate(
            ['job_number' => 'JOB-202610-0013'],
            [
                'branch_id' => $branchColombo->id,
                'date' => '2026-10-12',
                'customer_id' => $customerXyz->id,
                'vehicle_id' => $vehXyz1->id,
                'vehicle_type_id' => $typeLorry->id,
                'current_mileage' => 110000,
                'next_service_mileage' => 115000,
                'status' => 'completed',
                'service_total' => 20000.00,
                'product_total' => 30000.00,
                'grand_total' => 50000.00,
                'created_by' => $admin->id,
            ]
        );

        JobCardService::updateOrCreate(
            ['job_card_id' => $jobOctXyz1->id, 'service_id' => $oilService->id],
            [
                'service_code' => $oilService->code,
                'service_name' => 'Heavy Fleet Overhaul & Transmission Service',
                'unit_price' => 20000.00,
                'quantity' => 1,
                'line_total' => 20000.00,
            ]
        );

        $invXyz1 = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202610-0013'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerXyz->id,
                'job_card_id' => $jobOctXyz1->id,
                'invoice_date' => '2026-10-12',
                'due_date' => '2026-11-11',
                'invoice_type' => 'job_card',
                'subtotal' => 50000.00,
                'grand_total' => 50000.00,
                'amount_paid' => 0.00,
                'balance_amount' => 50000.00,
                'status' => 'issued',
                'payment_method' => 'credit',
                'notes' => 'Vehicle WP-NB-1122 Heavy Fleet Overhaul.',
                'created_by' => $admin->id,
            ]
        );
        $jobOctXyz1->update(['invoice_id' => $invXyz1->id]);

        $jobOctXyz2 = JobCard::updateOrCreate(
            ['job_number' => 'JOB-202610-0014'],
            [
                'branch_id' => $branchColombo->id,
                'date' => '2026-10-15',
                'customer_id' => $customerXyz->id,
                'vehicle_id' => $vehXyz2->id,
                'vehicle_type_id' => $typeLorry->id,
                'current_mileage' => 145000,
                'next_service_mileage' => 150000,
                'status' => 'completed',
                'service_total' => 15000.00,
                'product_total' => 25000.00,
                'grand_total' => 40000.00,
                'created_by' => $admin->id,
            ]
        );

        JobCardService::updateOrCreate(
            ['job_card_id' => $jobOctXyz2->id, 'service_id' => $oilService->id],
            [
                'service_code' => $oilService->code,
                'service_name' => 'Suspension & Air Brake System Servicing',
                'unit_price' => 15000.00,
                'quantity' => 1,
                'line_total' => 15000.00,
            ]
        );

        $invXyz2 = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202610-0014'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerXyz->id,
                'job_card_id' => $jobOctXyz2->id,
                'invoice_date' => '2026-10-15',
                'due_date' => '2026-11-14',
                'invoice_type' => 'job_card',
                'subtotal' => 40000.00,
                'grand_total' => 40000.00,
                'amount_paid' => 0.00,
                'balance_amount' => 40000.00,
                'status' => 'issued',
                'payment_method' => 'credit',
                'notes' => 'Vehicle WP-NB-3344 Suspension & Brake Service.',
                'created_by' => $admin->id,
            ]
        );
        $jobOctXyz2->update(['invoice_id' => $invXyz2->id]);

        // 5.5 Kamal Gunaratne: 2 Invoices (Total 30,000 LKR) with 10,000 LKR Cash Payment
        $invKamal1 = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202610-0015'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerKamal->id,
                'invoice_date' => '2026-10-08',
                'due_date' => '2026-10-22',
                'invoice_type' => 'job_card',
                'subtotal' => 18000.00,
                'grand_total' => 18000.00,
                'amount_paid' => 10000.00,
                'balance_amount' => 8000.00,
                'status' => 'partially_paid',
                'payment_method' => 'cash',
                'notes' => 'Vehicle WP-CAE-4554 Hybrid Diagnostics.',
                'created_by' => $admin->id,
            ]
        );

        Payment::updateOrCreate(
            ['payment_number' => 'PAY-202610-0015'],
            [
                'invoice_id' => $invKamal1->id,
                'customer_id' => $customerKamal->id,
                'payment_date' => '2026-10-16',
                'amount' => 10000.00,
                'payment_method' => 'cash',
                'notes' => 'Counter cash payment towards INV-202610-0015.',
                'status' => 'completed',
                'created_by' => $admin->id,
            ]
        );

        $invKamal2 = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202610-0016'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerKamal->id,
                'invoice_date' => '2026-10-16',
                'due_date' => '2026-10-30',
                'invoice_type' => 'job_card',
                'subtotal' => 12000.00,
                'grand_total' => 12000.00,
                'amount_paid' => 0.00,
                'balance_amount' => 12000.00,
                'status' => 'issued',
                'payment_method' => 'credit',
                'notes' => 'Vehicle WP-CAE-4554 Brake Pad Replacement.',
                'created_by' => $admin->id,
            ]
        );

        // =========================================================================
        // 6. CREDIT AGING TEST INVOICES (Across 1-30, 31-60, 61-90, 90+ Days Overdue)
        // =========================================================================
        // Current / Not Yet Due (Due 10 Nov 2026 - 15,000 LKR)
        Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202610-AG01'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerLanka->id,
                'invoice_date' => '2026-10-10',
                'due_date' => '2026-11-10',
                'invoice_type' => 'job_card',
                'subtotal' => 15000.00,
                'grand_total' => 15000.00,
                'amount_paid' => 0.00,
                'balance_amount' => 15000.00,
                'status' => 'issued',
                'payment_method' => 'credit',
                'created_by' => $admin->id,
            ]
        );

        // 1–30 Days Overdue (Due 05 Oct 2026 - 18,500 LKR, 15 days overdue as of 20 Oct)
        Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202609-0023'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerLanka->id,
                'invoice_date' => '2026-09-05',
                'due_date' => '2026-10-05',
                'invoice_type' => 'job_card',
                'subtotal' => 18500.00,
                'grand_total' => 18500.00,
                'amount_paid' => 0.00,
                'balance_amount' => 18500.00,
                'status' => 'issued',
                'payment_method' => 'credit',
                'created_by' => $admin->id,
            ]
        );

        // 31–60 Days Overdue (Due 05 Sep 2026 - 22,000 LKR, 45 days overdue as of 20 Oct)
        Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202608-0001'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerLanka->id,
                'invoice_date' => '2026-08-05',
                'due_date' => '2026-09-05',
                'invoice_type' => 'job_card',
                'subtotal' => 22000.00,
                'grand_total' => 22000.00,
                'amount_paid' => 0.00,
                'balance_amount' => 22000.00,
                'status' => 'issued',
                'payment_method' => 'credit',
                'created_by' => $admin->id,
            ]
        );

        // 61–90 Days Overdue (Due 05 Aug 2026 - 28,000 LKR, 76 days overdue as of 20 Oct)
        Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202607-0001'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerLanka->id,
                'invoice_date' => '2026-07-06',
                'due_date' => '2026-08-05',
                'invoice_type' => 'job_card',
                'subtotal' => 28000.00,
                'grand_total' => 28000.00,
                'amount_paid' => 0.00,
                'balance_amount' => 28000.00,
                'status' => 'issued',
                'payment_method' => 'credit',
                'created_by' => $admin->id,
            ]
        );

        // Over 90 Days Overdue (Due 15 Jun 2026 - 35,000 LKR, 127 days overdue as of 20 Oct)
        Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202605-0001'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerLanka->id,
                'invoice_date' => '2026-05-15',
                'due_date' => '2026-06-15',
                'invoice_type' => 'job_card',
                'subtotal' => 35000.00,
                'grand_total' => 35000.00,
                'amount_paid' => 0.00,
                'balance_amount' => 35000.00,
                'status' => 'issued',
                'payment_method' => 'credit',
                'created_by' => $admin->id,
            ]
        );

        // =========================================================================
        // 7. DIRECT SALES & CHEQUE TRANSACTIONS (FOR SALES & MONEY BOOK)
        // =========================================================================
        $custDilshan = Customer::find(16) ?? $customerKamal;
        $custKasun = Customer::find(13) ?? $customerKamal;
        $custPriyanka = Customer::find(15) ?? $customerKamal;
        $custRuwan = Customer::find(17) ?? $customerKamal;

        // 7.1 Direct Sale Cash: Oct 02 (6,500 LKR)
        $invOctCash = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202610-0017'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $custDilshan->id,
                'invoice_date' => '2026-10-02',
                'due_date' => '2026-10-02',
                'invoice_type' => 'direct_sale',
                'subtotal' => 6500.00,
                'grand_total' => 6500.00,
                'amount_paid' => 6500.00,
                'balance_amount' => 0.00,
                'status' => 'paid',
                'payment_method' => 'cash',
                'created_by' => $staff->id,
            ]
        );

        DirectSale::updateOrCreate(
            ['sale_number' => 'DS-202610-0017'],
            [
                'branch_id' => $branchColombo->id,
                'invoice_id' => $invOctCash->id,
                'customer_id' => $custDilshan->id,
                'sale_date' => '2026-10-02',
                'subtotal' => 6500.00,
                'grand_total' => 6500.00,
                'amount_paid' => 6500.00,
                'change_amount' => 0.00,
                'payment_method' => 'cash',
                'status' => 'completed',
                'created_by' => $staff->id,
            ]
        );

        Payment::updateOrCreate(
            ['payment_number' => 'PAY-202610-0017'],
            [
                'invoice_id' => $invOctCash->id,
                'customer_id' => $custDilshan->id,
                'payment_date' => '2026-10-02',
                'amount' => 6500.00,
                'payment_method' => 'cash',
                'status' => 'completed',
                'created_by' => $staff->id,
            ]
        );

        // 7.2 Direct Sale Card: Oct 07 (12,500 LKR)
        $invOctCard = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202610-0018'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $custKasun->id,
                'invoice_date' => '2026-10-07',
                'due_date' => '2026-10-07',
                'invoice_type' => 'direct_sale',
                'subtotal' => 12500.00,
                'grand_total' => 12500.00,
                'amount_paid' => 12500.00,
                'balance_amount' => 0.00,
                'status' => 'paid',
                'payment_method' => 'card',
                'created_by' => $staff->id,
            ]
        );

        Payment::updateOrCreate(
            ['payment_number' => 'PAY-202610-0018'],
            [
                'invoice_id' => $invOctCard->id,
                'customer_id' => $custKasun->id,
                'payment_date' => '2026-10-07',
                'amount' => 12500.00,
                'payment_method' => 'card',
                'reference_number' => 'AUTH-VISA-88991',
                'status' => 'completed',
                'created_by' => $staff->id,
            ]
        );

        // 7.3 Direct Sale Bank Transfer: Oct 11 (21,000 LKR)
        $invOctBank = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202610-0019'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $custPriyanka->id,
                'invoice_date' => '2026-10-11',
                'due_date' => '2026-10-11',
                'invoice_type' => 'direct_sale',
                'subtotal' => 21000.00,
                'grand_total' => 21000.00,
                'amount_paid' => 21000.00,
                'balance_amount' => 0.00,
                'status' => 'paid',
                'payment_method' => 'bank_transfer',
                'created_by' => $staff->id,
            ]
        );

        Payment::updateOrCreate(
            ['payment_number' => 'PAY-202610-0019'],
            [
                'invoice_id' => $invOctBank->id,
                'customer_id' => $custPriyanka->id,
                'payment_date' => '2026-10-11',
                'amount' => 21000.00,
                'payment_method' => 'bank_transfer',
                'reference_number' => 'TRX-HNB-334411',
                'status' => 'completed',
                'created_by' => $staff->id,
            ]
        );

        // 7.4 Cheque Sale (Deposited): Oct 15 (15,000 LKR)
        $invOctChq = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202610-0020'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $custRuwan->id,
                'invoice_date' => '2026-10-15',
                'due_date' => '2026-10-15',
                'invoice_type' => 'direct_sale',
                'subtotal' => 15000.00,
                'grand_total' => 15000.00,
                'amount_paid' => 15000.00,
                'balance_amount' => 0.00,
                'status' => 'paid',
                'payment_method' => 'cheque',
                'created_by' => $staff->id,
            ]
        );

        Payment::updateOrCreate(
            ['payment_number' => 'PAY-202610-0020'],
            [
                'invoice_id' => $invOctChq->id,
                'customer_id' => $custRuwan->id,
                'payment_date' => '2026-10-15',
                'amount' => 15000.00,
                'payment_method' => 'cheque',
                'reference_number' => 'CHQ-889902',
                'notes' => '[Status: Deposited] Sampath Bank Chq #889902',
                'status' => 'completed',
                'created_by' => $staff->id,
            ]
        );

        // 7.5 Cancelled Sale (Void test): Oct 19 (9,000 LKR)
        Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202610-0021'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $custDilshan->id,
                'invoice_date' => '2026-10-19',
                'due_date' => '2026-10-19',
                'invoice_type' => 'direct_sale',
                'subtotal' => 9000.00,
                'grand_total' => 9000.00,
                'amount_paid' => 0.00,
                'balance_amount' => 9000.00,
                'status' => 'cancelled',
                'cancellation_reason' => 'Customer cancelled item selection prior to counter packaging.',
                'cancelled_by' => $admin->id,
                'cancelled_at' => '2026-10-19 14:30:00',
                'created_by' => $staff->id,
            ]
        );

        // 7.6 Kandy Branch Direct Sale: Oct 20 (14,500 LKR)
        $invKandy = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202610-0022'],
            [
                'branch_id' => $branchKandy->id,
                'customer_id' => $customerKamal->id,
                'invoice_date' => '2026-10-20',
                'due_date' => '2026-10-20',
                'invoice_type' => 'direct_sale',
                'subtotal' => 14500.00,
                'grand_total' => 14500.00,
                'amount_paid' => 14500.00,
                'balance_amount' => 0.00,
                'status' => 'paid',
                'payment_method' => 'cash',
                'created_by' => $staff->id,
            ]
        );

        Payment::updateOrCreate(
            ['payment_number' => 'PAY-202610-0022'],
            [
                'invoice_id' => $invKandy->id,
                'customer_id' => $customerKamal->id,
                'payment_date' => '2026-10-20',
                'amount' => 14500.00,
                'payment_method' => 'cash',
                'status' => 'completed',
                'created_by' => $staff->id,
            ]
        );

        // 7.7 Cheque States: Cleared, Received, Bounced/Reversed
        // Cleared Cheque (Sep 25)
        $invSepChq = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202609-0005'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $custKasun->id,
                'invoice_date' => '2026-09-25',
                'due_date' => '2026-09-25',
                'invoice_type' => 'direct_sale',
                'subtotal' => 11000.00,
                'grand_total' => 11000.00,
                'amount_paid' => 11000.00,
                'balance_amount' => 0.00,
                'status' => 'paid',
                'payment_method' => 'cheque',
                'created_by' => $staff->id,
            ]
        );

        Payment::updateOrCreate(
            ['payment_number' => 'PAY-202609-0005'],
            [
                'invoice_id' => $invSepChq->id,
                'customer_id' => $custKasun->id,
                'payment_date' => '2026-09-25',
                'amount' => 11000.00,
                'payment_method' => 'cheque',
                'reference_number' => 'CHQ-889901',
                'notes' => '[Status: Cleared] Commercial Bank Chq #889901 cleared.',
                'status' => 'completed',
                'created_by' => $staff->id,
            ]
        );

        // Bounced / Reversed Cheque Payment (Oct 12, 8,000 LKR)
        Payment::updateOrCreate(
            ['payment_number' => 'PAY-202610-0024'],
            [
                'invoice_id' => $invKamal1->id,
                'customer_id' => $customerKamal->id,
                'payment_date' => '2026-10-12',
                'amount' => 8000.00,
                'payment_method' => 'cheque',
                'reference_number' => 'CHQ-889904',
                'notes' => '[Status: Bounced] Seylan Bank Chq returned.',
                'status' => 'reversed',
                'reversed_at' => '2026-10-13 10:00:00',
                'reversal_reason' => 'Cheque bounced due to insufficient drawer funds.',
                'created_by' => $staff->id,
            ]
        );

        // 7.8 MONEY BOOK SPECIFIC DAILY SALES TEST (Oct 17, 2026)
        // Daily Sales: Cash: 20k, Card: 15k, Cheque: 10k, Credit: 5k => Total Daily Sales: 50,000 LKR
        // Receipts: Cash 20k, Card 15k, Cheque 10k | Outstanding Credit Created: 5k | Liquid Inflow: 35k
        $invMbCash = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202610-0030'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerKamal->id,
                'invoice_date' => '2026-10-17',
                'due_date' => '2026-10-17',
                'invoice_type' => 'direct_sale',
                'subtotal' => 20000.00,
                'grand_total' => 20000.00,
                'amount_paid' => 20000.00,
                'balance_amount' => 0.00,
                'status' => 'paid',
                'payment_method' => 'cash',
                'created_by' => $staff->id,
            ]
        );

        Payment::updateOrCreate(
            ['payment_number' => 'PAY-202610-0030'],
            [
                'invoice_id' => $invMbCash->id,
                'customer_id' => $customerKamal->id,
                'payment_date' => '2026-10-17',
                'amount' => 20000.00,
                'payment_method' => 'cash',
                'status' => 'completed',
                'created_by' => $staff->id,
            ]
        );

        $invMbCard = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202610-0031'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerKamal->id,
                'invoice_date' => '2026-10-17',
                'due_date' => '2026-10-17',
                'invoice_type' => 'direct_sale',
                'subtotal' => 15000.00,
                'grand_total' => 15000.00,
                'amount_paid' => 15000.00,
                'balance_amount' => 0.00,
                'status' => 'paid',
                'payment_method' => 'card',
                'created_by' => $staff->id,
            ]
        );

        Payment::updateOrCreate(
            ['payment_number' => 'PAY-202610-0031'],
            [
                'invoice_id' => $invMbCard->id,
                'customer_id' => $customerKamal->id,
                'payment_date' => '2026-10-17',
                'amount' => 15000.00,
                'payment_method' => 'card',
                'reference_number' => 'AUTH-MC-55442',
                'status' => 'completed',
                'created_by' => $staff->id,
            ]
        );

        $invMbChq = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202610-0032'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $customerKamal->id,
                'invoice_date' => '2026-10-17',
                'due_date' => '2026-10-17',
                'invoice_type' => 'direct_sale',
                'subtotal' => 10000.00,
                'grand_total' => 10000.00,
                'amount_paid' => 10000.00,
                'balance_amount' => 0.00,
                'status' => 'paid',
                'payment_method' => 'cheque',
                'created_by' => $staff->id,
            ]
        );

        Payment::updateOrCreate(
            ['payment_number' => 'PAY-202610-0032'],
            [
                'invoice_id' => $invMbChq->id,
                'customer_id' => $customerKamal->id,
                'payment_date' => '2026-10-17',
                'amount' => 10000.00,
                'payment_method' => 'cheque',
                'reference_number' => 'CHQ-771122',
                'notes' => '[Status: Received] HNB Cheque received, pending bank deposit.',
                'status' => 'completed',
                'created_by' => $staff->id,
            ]
        );

        $invMbCredit = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202610-0033'],
            [
                'branch_id' => $branchColombo->id,
                'customer_id' => $custDilshan->id,
                'invoice_date' => '2026-10-17',
                'due_date' => '2026-11-15',
                'invoice_type' => 'job_card',
                'subtotal' => 5000.00,
                'grand_total' => 5000.00,
                'amount_paid' => 0.00,
                'balance_amount' => 5000.00,
                'status' => 'issued',
                'payment_method' => 'credit',
                'created_by' => $staff->id,
            ]
        );

        // =========================================================================
        // 8. OPERATING EXPENSES & NON-DOUBLE-COUNT ITEMS (PROFIT & LOSS)
        // =========================================================================
        $catElectric = ExpenseCategory::firstOrCreate(['name' => 'Electricity & Power'], ['is_active' => true]);
        $catWater = ExpenseCategory::firstOrCreate(['name' => 'Water Utilities'], ['is_active' => true]);
        $catTelecom = ExpenseCategory::firstOrCreate(['name' => 'Telephone & Internet'], ['is_active' => true]);
        $catRepairs = ExpenseCategory::firstOrCreate(['name' => 'Repairs & Maintenance'], ['is_active' => true]);
        $catConsumables = ExpenseCategory::firstOrCreate(['name' => 'Workshop Consumables & Chemicals'], ['is_active' => true]);
        $catAdvance = ExpenseCategory::firstOrCreate(['name' => 'Salary Advance'], ['is_active' => true]);

        // September Operating Expenses
        Expense::updateOrCreate(
            ['expense_number' => 'EXP-202609-0001'],
            [
                'branch_id' => $branchColombo->id,
                'category_id' => $catElectric->id,
                'expense_type' => Expense::TYPE_SHOP,
                'description' => 'CEB Electricity Bill - September 2026',
                'amount' => 24500.00,
                'expense_date' => '2026-09-28',
                'related_month' => '2026-09',
                'payment_method' => 'bank_transfer',
                'status' => Expense::STATUS_ACTIVE,
                'created_by' => $admin->id,
            ]
        );

        Expense::updateOrCreate(
            ['expense_number' => 'EXP-202609-0002'],
            [
                'branch_id' => $branchColombo->id,
                'category_id' => $catWater->id,
                'expense_type' => Expense::TYPE_SHOP,
                'description' => 'National Water Board - September 2026',
                'amount' => 4200.00,
                'expense_date' => '2026-09-28',
                'related_month' => '2026-09',
                'payment_method' => 'cash',
                'status' => Expense::STATUS_ACTIVE,
                'created_by' => $admin->id,
            ]
        );

        Expense::updateOrCreate(
            ['expense_number' => 'EXP-202609-0003'],
            [
                'branch_id' => $branchColombo->id,
                'category_id' => $catTelecom->id,
                'expense_type' => Expense::TYPE_SHOP,
                'description' => 'SLT Fiber Broadband & Telephone - Sep 2026',
                'amount' => 6800.00,
                'expense_date' => '2026-09-25',
                'related_month' => '2026-09',
                'payment_method' => 'card',
                'status' => Expense::STATUS_ACTIVE,
                'created_by' => $admin->id,
            ]
        );

        Expense::updateOrCreate(
            ['expense_number' => 'EXP-202609-0004'],
            [
                'branch_id' => $branchColombo->id,
                'category_id' => $catRepairs->id,
                'expense_type' => Expense::TYPE_SHOP,
                'description' => 'Hydraulic Hoist #2 Servicing & Seal Repair',
                'amount' => 12000.00,
                'expense_date' => '2026-09-15',
                'related_month' => '2026-09',
                'payment_method' => 'cash',
                'status' => Expense::STATUS_ACTIVE,
                'created_by' => $admin->id,
            ]
        );

        Expense::updateOrCreate(
            ['expense_number' => 'EXP-202609-0005'],
            [
                'branch_id' => $branchColombo->id,
                'category_id' => $catConsumables->id,
                'expense_type' => Expense::TYPE_SHOP,
                'description' => 'Degreaser & Workshop Floor Wash Chemicals',
                'amount' => 5500.00,
                'expense_date' => '2026-09-12',
                'related_month' => '2026-09',
                'payment_method' => 'cash',
                'status' => Expense::STATUS_ACTIVE,
                'created_by' => $admin->id,
            ]
        );

        // October Operating Expenses
        Expense::updateOrCreate(
            ['expense_number' => 'EXP-202610-0010'],
            [
                'branch_id' => $branchColombo->id,
                'category_id' => $catElectric->id,
                'expense_type' => Expense::TYPE_SHOP,
                'description' => 'CEB Electricity Bill - October 2026',
                'amount' => 26000.00,
                'expense_date' => '2026-10-18',
                'related_month' => '2026-10',
                'payment_method' => 'bank_transfer',
                'status' => Expense::STATUS_ACTIVE,
                'created_by' => $admin->id,
            ]
        );

        Expense::updateOrCreate(
            ['expense_number' => 'EXP-202610-0011'],
            [
                'branch_id' => $branchColombo->id,
                'category_id' => $catWater->id,
                'expense_type' => Expense::TYPE_SHOP,
                'description' => 'Water Utility Supply - October 2026',
                'amount' => 4500.00,
                'expense_date' => '2026-10-18',
                'related_month' => '2026-10',
                'payment_method' => 'cash',
                'status' => Expense::STATUS_ACTIVE,
                'created_by' => $admin->id,
            ]
        );

        Expense::updateOrCreate(
            ['expense_number' => 'EXP-202610-0012'],
            [
                'branch_id' => $branchColombo->id,
                'category_id' => $catTelecom->id,
                'expense_type' => Expense::TYPE_SHOP,
                'description' => 'Telecom Broadband - October 2026',
                'amount' => 7200.00,
                'expense_date' => '2026-10-15',
                'related_month' => '2026-10',
                'payment_method' => 'card',
                'status' => Expense::STATUS_ACTIVE,
                'created_by' => $admin->id,
            ]
        );

        Expense::updateOrCreate(
            ['expense_number' => 'EXP-202610-0013'],
            [
                'branch_id' => $branchColombo->id,
                'category_id' => $catRepairs->id,
                'expense_type' => Expense::TYPE_SHOP,
                'description' => 'Wheel Balancer Calibration & Sensor Maintenance',
                'amount' => 15000.00,
                'expense_date' => '2026-10-09',
                'related_month' => '2026-10',
                'payment_method' => 'cash',
                'status' => Expense::STATUS_ACTIVE,
                'created_by' => $admin->id,
            ]
        );

        Expense::updateOrCreate(
            ['expense_number' => 'EXP-202610-0014'],
            [
                'branch_id' => $branchColombo->id,
                'category_id' => $catConsumables->id,
                'expense_type' => Expense::TYPE_SHOP,
                'description' => 'Bulk Microfiber Towels & Surface Cleaners',
                'amount' => 6200.00,
                'expense_date' => '2026-10-04',
                'related_month' => '2026-10',
                'payment_method' => 'cash',
                'status' => Expense::STATUS_ACTIVE,
                'created_by' => $admin->id,
            ]
        );

        // 8.1 NON-DOUBLE-COUNT ITEM: Salary Advance (Should NOT be in Operating Expenses!)
        $employee = Employee::first();
        if ($employee) {
            $advance = EmployeeAdvance::updateOrCreate(
                ['advance_number' => 'ADV-202610-001'],
                [
                    'employee_id' => $employee->id,
                    'advance_date' => '2026-10-10',
                    'amount' => 15000.00,
                    'payment_method' => 'cash',
                    'notes' => 'Mid-month personal salary advance - to be deducted at month-end.',
                    'status' => EmployeeAdvance::STATUS_PENDING,
                    'created_by' => $admin->id,
                ]
            );

            Expense::updateOrCreate(
                ['expense_number' => 'EXP-202610-ADV01'],
                [
                    'branch_id' => $branchColombo->id,
                    'category_id' => $catAdvance->id,
                    'employee_advance_id' => $advance->id,
                    'expense_type' => Expense::TYPE_SHOP,
                    'description' => 'Salary Advance - '.$employee->first_name,
                    'amount' => 15000.00,
                    'expense_date' => '2026-10-10',
                    'related_month' => '2026-10',
                    'payment_method' => 'cash',
                    'status' => Expense::STATUS_ACTIVE,
                    'created_by' => $admin->id,
                ]
            );
        }

        // 8.2 NON-DOUBLE-COUNT ITEM: Supplier GRN Payment (Should NOT be in Operating Expenses!)
        $grn = GoodsReceivedNote::first();
        if ($grn) {
            SupplierPayment::updateOrCreate(
                ['payment_number' => 'SUP-PAY-202610-001'],
                [
                    'supplier_id' => $supplier->id,
                    'branch_id' => $branchColombo->id,
                    'grn_id' => $grn->id,
                    'payment_date' => '2026-10-14',
                    'amount' => 45000.00,
                    'payment_method' => 'bank_transfer',
                    'reference_number' => 'TRX-SUP-443322',
                    'notes' => 'Partial settlement for verified inventory GRN.',
                    'status' => 'completed',
                    'created_by' => $admin->id,
                ]
            );
        }

        // 8.3 Set October Payroll Period to 'approved' so its salary expenses reflect in October P&L
        $octPayroll = PayrollPeriod::where('year', 2026)->where('month', 10)->first();
        if ($octPayroll && $octPayroll->status === PayrollPeriod::STATUS_DRAFT) {
            $octPayroll->update([
                'status' => PayrollPeriod::STATUS_APPROVED,
                'approved_by' => $admin->id,
                'approved_at' => '2026-10-20 09:00:00',
            ]);
        }
    }
}
