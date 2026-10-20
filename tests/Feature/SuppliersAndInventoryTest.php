<?php

namespace Tests\Feature;

use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\Supplier;
use App\Models\UnitOfMeasurement;
use App\Models\User;
use App\Services\Inventory\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class SuppliersAndInventoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected UnitOfMeasurement $itemUom;

    protected UnitOfMeasurement $canUom;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->staff = User::factory()->staff()->create();

        $this->itemUom = UnitOfMeasurement::firstOrCreate(
            ['name' => 'Item'],
            ['code' => 'ITM', 'is_active' => true]
        );

        $this->canUom = UnitOfMeasurement::firstOrCreate(
            ['name' => 'Can'],
            ['code' => 'CAN', 'is_active' => true]
        );
    }

    public function test_admin_can_add_supplier(): void
    {
        $response = $this->actingAs($this->admin)->post(route('inventory.suppliers.store'), [
            'name' => 'B.S.K. Motors',
            'registration_number' => 'PV-99881',
            'mobile' => '0771234567',
            'office_phone' => '0112345678',
            'address' => 'Colombo 10, Sri Lanka',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('inventory.suppliers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('suppliers', [
            'name' => 'B.S.K. Motors',
            'registration_number' => 'PV-99881',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_edit_supplier(): void
    {
        $supplier = Supplier::create([
            'name' => 'Jayasekara Parts',
            'mobile' => '0711112222',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('inventory.suppliers.update', $supplier), [
            'name' => 'Jayasekara Distributors (Pvt) Ltd',
            'mobile' => '0711113333',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('inventory.suppliers.index'));
        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Jayasekara Distributors (Pvt) Ltd',
            'mobile' => '0711113333',
        ]);
    }

    public function test_staff_cannot_create_or_edit_supplier(): void
    {
        $response = $this->actingAs($this->staff)->post(route('inventory.suppliers.store'), [
            'name' => 'Unauthorized Vendor',
        ]);
        $response->assertStatus(403);

        $supplier = Supplier::create(['name' => 'Test Vendor', 'is_active' => true]);
        $response2 = $this->actingAs($this->staff)->put(route('inventory.suppliers.update', $supplier), [
            'name' => 'Hacked Vendor',
        ]);
        $response2->assertStatus(403);
    }

    public function test_admin_can_add_product_with_uom_and_supplier(): void
    {
        $supplier = Supplier::create(['name' => 'B.S.K. Motors', 'is_active' => true]);

        $response = $this->actingAs($this->admin)->post(route('inventory.products.store'), [
            'name' => 'C111 JS Oil Filter',
            'sku' => 'OF-C111',
            'uom_id' => $this->itemUom->id,
            'preferred_supplier_id' => $supplier->id,
            'selling_price' => 950.00,
            'min_stock' => 10,
            'max_stock' => 100,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('inventory.products.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'C111 JS Oil Filter',
            'sku' => 'OF-C111',
            'uom_id' => $this->itemUom->id,
            'preferred_supplier_id' => $supplier->id,
            'selling_price' => 950.00,
        ]);
    }

    public function test_staff_can_view_products_but_cannot_create_them(): void
    {
        $product = Product::create([
            'name' => 'Engine Oil 1L',
            'sku' => 'OIL-001',
            'uom_id' => $this->canUom->id,
            'selling_price' => 2500.00,
            'is_active' => true,
        ]);

        $viewResponse = $this->actingAs($this->staff)->get(route('inventory.products.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Engine Oil 1L');

        $createResponse = $this->actingAs($this->staff)->post(route('inventory.products.store'), [
            'name' => 'Unauthorized Product',
            'uom_id' => $this->itemUom->id,
            'selling_price' => 100,
        ]);
        $createResponse->assertStatus(403);
    }

    public function test_create_draft_grn_does_not_increase_stock(): void
    {
        $supplier = Supplier::create(['name' => 'B.S.K. Motors', 'is_active' => true]);
        $product = Product::create([
            'name' => 'Brake Fluid DOT4',
            'uom_id' => $this->canUom->id,
            'selling_price' => 1500.00,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('inventory.grn.store'), [
            'supplier_id' => $supplier->id,
            'supplier_invoice_number' => 'INV-5501',
            'received_date' => '2026-10-19',
            'action' => 'draft',
            'items' => [
                [
                    'product_id' => $product->id,
                    'purchase_quantity' => 10,
                    'free_quantity' => 2,
                    'purchase_unit_price' => 400.00,
                    'selling_price_snapshot' => 1500.00,
                ],
            ],
        ]);

        $response->assertSessionHas('success');
        $grn = GoodsReceivedNote::where('supplier_invoice_number', 'INV-5501')->firstOrFail();
        $this->assertTrue($grn->isDraft());
        $this->assertEquals(4000.00, (float) $grn->total_amount);

        // No batches created yet and stock must be 0
        $this->assertEquals(0, StockBatch::where('product_id', $product->id)->count());
        $this->assertEquals(0.0, $product->available_stock);
    }

    public function test_confirm_grn_creates_batch_with_allocated_effective_cost(): void
    {
        $supplier = Supplier::create(['name' => 'B.S.K. Motors', 'is_active' => true]);
        $product = Product::create([
            'name' => 'C111 JS Oil Filter',
            'uom_id' => $this->itemUom->id,
            'selling_price' => 950.00,
            'is_active' => true,
        ]);

        $grn = GoodsReceivedNote::create([
            'grn_number' => 'GRN-TEST-001',
            'supplier_id' => $supplier->id,
            'supplier_invoice_number' => 'INV-1005',
            'received_date' => '2026-10-19',
            'status' => 'draft',
            'total_amount' => 4000.00,
            'created_by' => $this->admin->id,
        ]);

        $item = GoodsReceivedNoteItem::create([
            'grn_id' => $grn->id,
            'product_id' => $product->id,
            'purchase_quantity' => 10,
            'free_quantity' => 2,
            'purchase_unit_price' => 400.00,
            'selling_price_snapshot' => 950.00,
            'line_total' => 4000.00,
        ]);

        $stockService = app(StockService::class);
        $stockService->confirmGrn($grn, $this->admin);

        $grn->refresh();
        $this->assertTrue($grn->isConfirmed());

        // Stock Batch check:
        // Physical stock = 10 + 2 = 12 units
        // Invoice price = 400.00
        // Effective unit cost = 4000 / 12 = 333.3333
        $batch = StockBatch::where('grn_id', $grn->id)->where('product_id', $product->id)->firstOrFail();
        $this->assertEquals(12.0, (float) $batch->received_quantity);
        $this->assertEquals(12.0, (float) $batch->remaining_quantity);
        $this->assertEquals(400.00, (float) $batch->original_purchase_unit_price);
        $this->assertEquals(333.3333, (float) $batch->inventory_unit_cost);

        // Product available stock is now 12
        $this->assertEquals(12.0, $product->fresh()->available_stock);
    }

    public function test_cannot_confirm_grn_twice(): void
    {
        $supplier = Supplier::create(['name' => 'B.S.K. Motors', 'is_active' => true]);
        $product = Product::create([
            'name' => 'Wiper Blade',
            'uom_id' => $this->itemUom->id,
            'selling_price' => 1200.00,
            'is_active' => true,
        ]);

        $grn = GoodsReceivedNote::create([
            'grn_number' => 'GRN-TEST-002',
            'supplier_id' => $supplier->id,
            'received_date' => '2026-10-19',
            'status' => 'draft',
            'total_amount' => 1000.00,
            'created_by' => $this->admin->id,
        ]);

        GoodsReceivedNoteItem::create([
            'grn_id' => $grn->id,
            'product_id' => $product->id,
            'purchase_quantity' => 5,
            'purchase_unit_price' => 200.00,
            'line_total' => 1000.00,
        ]);

        $stockService = app(StockService::class);
        $stockService->confirmGrn($grn, $this->admin);

        // Second confirmation attempt must throw exception
        $this->expectException(RuntimeException::class);
        $stockService->confirmGrn($grn, $this->admin);
    }

    public function test_batch_wise_stock_management_preserves_old_and_new_prices(): void
    {
        $supplier = Supplier::create(['name' => 'B.S.K. Motors', 'is_active' => true]);
        $product = Product::create([
            'name' => 'Engine Oil 1L',
            'uom_id' => $this->canUom->id,
            'selling_price' => 2500.00,
            'is_active' => true,
        ]);

        $stockService = app(StockService::class);

        // 1st purchase: 10 units at LKR 800 (Total 8,000)
        $grn1 = GoodsReceivedNote::create([
            'grn_number' => 'GRN-BATCH-001',
            'supplier_id' => $supplier->id,
            'received_date' => '2026-01-02',
            'status' => 'draft',
            'total_amount' => 8000.00,
            'created_by' => $this->admin->id,
        ]);
        GoodsReceivedNoteItem::create([
            'grn_id' => $grn1->id,
            'product_id' => $product->id,
            'purchase_quantity' => 10,
            'purchase_unit_price' => 800.00,
            'line_total' => 8000.00,
        ]);
        $stockService->confirmGrn($grn1, $this->admin);

        // 2nd purchase: 10 units at LKR 950 (Total 9,500)
        $grn2 = GoodsReceivedNote::create([
            'grn_number' => 'GRN-BATCH-002',
            'supplier_id' => $supplier->id,
            'received_date' => '2026-02-10',
            'status' => 'draft',
            'total_amount' => 9500.00,
            'created_by' => $this->admin->id,
        ]);
        GoodsReceivedNoteItem::create([
            'grn_id' => $grn2->id,
            'product_id' => $product->id,
            'purchase_quantity' => 10,
            'purchase_unit_price' => 950.00,
            'line_total' => 9500.00,
        ]);
        $stockService->confirmGrn($grn2, $this->admin);

        // Verify two distinct batches exist
        $batch1 = StockBatch::where('grn_id', $grn1->id)->firstOrFail();
        $batch2 = StockBatch::where('grn_id', $grn2->id)->firstOrFail();

        $this->assertEquals(800.00, (float) $batch1->inventory_unit_cost);
        $this->assertEquals(10.0, (float) $batch1->remaining_quantity);

        $this->assertEquals(950.00, (float) $batch2->inventory_unit_cost);
        $this->assertEquals(10.0, (float) $batch2->remaining_quantity);

        // Total stock is 20, Total Inventory Value is 17,500
        $product->refresh();
        $this->assertEquals(20.0, $product->available_stock);
        $this->assertEquals(17500.00, $product->inventory_value);
    }

    public function test_fifo_stock_deduction_and_negative_stock_prevention(): void
    {
        $supplier = Supplier::create(['name' => 'B.S.K. Motors', 'is_active' => true]);
        $product = Product::create([
            'name' => 'Engine Oil 1L',
            'uom_id' => $this->canUom->id,
            'selling_price' => 2500.00,
            'is_active' => true,
        ]);

        $stockService = app(StockService::class);

        // Create parent GRN & items for foreign key integrity
        $grn1 = GoodsReceivedNote::create([
            'grn_number' => 'GRN-FIFO-001',
            'supplier_id' => $supplier->id,
            'received_date' => '2026-01-02',
            'status' => 'confirmed',
            'total_amount' => 8000.00,
        ]);
        $item1 = GoodsReceivedNoteItem::create([
            'grn_id' => $grn1->id,
            'product_id' => $product->id,
            'purchase_quantity' => 10,
            'purchase_unit_price' => 800.00,
            'line_total' => 8000.00,
        ]);

        $grn2 = GoodsReceivedNote::create([
            'grn_number' => 'GRN-FIFO-002',
            'supplier_id' => $supplier->id,
            'received_date' => '2026-02-10',
            'status' => 'confirmed',
            'total_amount' => 9500.00,
        ]);
        $item2 = GoodsReceivedNoteItem::create([
            'grn_id' => $grn2->id,
            'product_id' => $product->id,
            'purchase_quantity' => 10,
            'purchase_unit_price' => 950.00,
            'line_total' => 9500.00,
        ]);

        // Batch 1: 10 units at 800 (date: 2026-01-02)
        $batch1 = StockBatch::create([
            'batch_number' => 'B001',
            'grn_id' => $grn1->id,
            'grn_item_id' => $item1->id,
            'product_id' => $product->id,
            'supplier_id' => $supplier->id,
            'purchase_date' => '2026-01-02',
            'received_quantity' => 10,
            'remaining_quantity' => 10,
            'original_purchase_unit_price' => 800.00,
            'inventory_unit_cost' => 800.00,
            'status' => 'active',
        ]);

        // Batch 2: 10 units at 950 (date: 2026-02-10)
        $batch2 = StockBatch::create([
            'batch_number' => 'B002',
            'grn_id' => $grn2->id,
            'grn_item_id' => $item2->id,
            'product_id' => $product->id,
            'supplier_id' => $supplier->id,
            'purchase_date' => '2026-02-10',
            'received_quantity' => 10,
            'remaining_quantity' => 10,
            'original_purchase_unit_price' => 950.00,
            'inventory_unit_cost' => 950.00,
            'status' => 'active',
        ]);

        // Customer buys 12 units
        // FIFO deduction: 10 from B001 (cost: 8,000) and 2 from B002 (cost: 1,900)
        // Total cost of sold stock = 9,900
        $result = $stockService->issueStockFifo($product, 12.0, 'fifo_sale', null, $this->admin);

        $this->assertEquals(12.0, $result['total_quantity']);
        $this->assertEquals(9900.00, $result['total_cost']);

        $batch1->refresh();
        $batch2->refresh();

        $this->assertEquals(0.0, (float) $batch1->remaining_quantity);
        $this->assertEquals('exhausted', $batch1->status);

        $this->assertEquals(8.0, (float) $batch2->remaining_quantity);
        $this->assertEquals('active', $batch2->status);

        // Remaining inventory value = 8 * 950 = 7,600
        $this->assertEquals(8.0, $product->fresh()->available_stock);
        $this->assertEquals(7600.00, $product->fresh()->inventory_value);

        // Attempting to deduct 15 units when only 8 are available must fail (prevent negative stock)
        $this->expectException(RuntimeException::class);
        $stockService->issueStockFifo($product, 15.0);
    }

    public function test_supplier_invoice_payments_can_be_recorded(): void
    {
        $supplier = Supplier::create(['name' => 'B.S.K. Motors', 'is_active' => true]);

        $grn = GoodsReceivedNote::create([
            'grn_number' => 'GRN-PAY-001',
            'supplier_id' => $supplier->id,
            'supplier_invoice_number' => 'INV-9900',
            'received_date' => '2026-10-19',
            'status' => 'confirmed',
            'total_amount' => 10000.00,
            'created_by' => $this->admin->id,
        ]);

        $this->assertEquals(10000.00, $grn->outstanding_amount);
        $this->assertEquals('Unpaid', $grn->payment_status);

        // Make partial payment of 4,000
        $response = $this->actingAs($this->admin)->post(route('inventory.grn.payments.store', $grn), [
            'payment_date' => '2026-10-19',
            'amount' => 4000.00,
            'payment_method' => 'bank_transfer',
            'reference_number' => 'TRX-102938',
        ]);

        $response->assertSessionHas('success');
        $grn->refresh();
        $this->assertEquals(4000.00, $grn->paid_amount);
        $this->assertEquals(6000.00, $grn->outstanding_amount);
        $this->assertEquals('Partially Paid', $grn->payment_status);

        // Settle remaining 6,000
        $this->actingAs($this->admin)->post(route('inventory.grn.payments.store', $grn), [
            'payment_date' => '2026-10-20',
            'amount' => 6000.00,
            'payment_method' => 'cheque',
            'reference_number' => 'CHQ-88221',
        ]);

        $grn->refresh();
        $this->assertEquals(10000.00, $grn->paid_amount);
        $this->assertEquals(0.00, $grn->outstanding_amount);
        $this->assertEquals('Paid', $grn->payment_status);
    }

    public function test_stock_report_batch_details_and_price_history_endpoints(): void
    {
        $supplier = Supplier::create(['name' => 'B.S.K. Motors', 'is_active' => true]);
        $product = Product::create([
            'name' => 'C111 JS Oil Filter',
            'sku' => 'OF-C111',
            'uom_id' => $this->itemUom->id,
            'selling_price' => 950.00,
            'is_active' => true,
        ]);

        $grn = GoodsReceivedNote::create([
            'grn_number' => 'GRN-HIST-001',
            'supplier_id' => $supplier->id,
            'received_date' => '2026-01-02',
            'status' => 'confirmed',
            'total_amount' => 3881.30,
        ]);
        $grnItem = GoodsReceivedNoteItem::create([
            'grn_id' => $grn->id,
            'product_id' => $product->id,
            'purchase_quantity' => 10,
            'purchase_unit_price' => 388.13,
            'line_total' => 3881.30,
        ]);

        $batch = StockBatch::create([
            'batch_number' => 'B001',
            'grn_id' => $grn->id,
            'grn_item_id' => $grnItem->id,
            'product_id' => $product->id,
            'supplier_id' => $supplier->id,
            'purchase_date' => '2026-01-02',
            'received_quantity' => 10,
            'remaining_quantity' => 3,
            'original_purchase_unit_price' => 388.13,
            'inventory_unit_cost' => 388.13,
            'status' => 'active',
        ]);

        // Test Batch Details JSON endpoint
        $batchResponse = $this->actingAs($this->staff)->get(route('inventory.stock-report.batches', $product));
        $batchResponse->assertStatus(200);
        $batchResponse->assertJsonFragment(['batch_number' => 'B001']);

        // Test Price History JSON endpoint
        $historyResponse = $this->actingAs($this->staff)->get(route('inventory.stock-report.price-history', $product));
        $historyResponse->assertStatus(200);
        $historyResponse->assertJsonFragment(['purchase_date' => '2026-01-02']);
    }

    public function test_suppliers_and_stock_report_export_csv(): void
    {
        Supplier::create(['name' => 'Exportable Supplier', 'is_active' => true]);
        Product::create([
            'name' => 'Exportable Product',
            'sku' => 'EXP-001',
            'uom_id' => $this->itemUom->id,
            'selling_price' => 500.00,
            'is_active' => true,
        ]);

        $supplierExport = $this->actingAs($this->admin)->get(route('inventory.suppliers.export'));
        $supplierExport->assertStatus(200);
        $supplierExport->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $stockExport = $this->actingAs($this->admin)->get(route('inventory.stock-report.export'));
        $stockExport->assertStatus(200);
        $stockExport->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_staff_cannot_confirm_draft_grn(): void
    {
        $supplier = Supplier::create(['name' => 'B.S.K. Motors', 'is_active' => true]);
        $product = Product::create([
            'name' => 'Gear Oil',
            'uom_id' => $this->canUom->id,
            'selling_price' => 2000.00,
            'is_active' => true,
        ]);

        $grn = GoodsReceivedNote::create([
            'grn_number' => 'GRN-DRAFT-009',
            'supplier_id' => $supplier->id,
            'received_date' => '2026-10-19',
            'status' => 'draft',
            'total_amount' => 1000.00,
        ]);
        GoodsReceivedNoteItem::create([
            'grn_id' => $grn->id,
            'product_id' => $product->id,
            'purchase_quantity' => 1,
            'purchase_unit_price' => 1000.00,
            'line_total' => 1000.00,
        ]);

        $response = $this->actingAs($this->staff)->patch(route('inventory.grn.confirm', $grn));
        $response->assertStatus(403);
    }

    public function test_supplier_and_product_search_and_pagination(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            Supplier::create([
                'name' => sprintf('Supplier %02d', $i),
                'is_active' => true,
            ]);
            Product::create([
                'name' => sprintf('Product %02d', $i),
                'sku' => sprintf('SKU-%02d', $i),
                'uom_id' => $this->itemUom->id,
                'selling_price' => 100.00 * $i,
                'is_active' => true,
            ]);
        }

        // Supplier list pagination (10 per page)
        $supplierList = $this->actingAs($this->staff)->get(route('inventory.suppliers.index'));
        $supplierList->assertStatus(200);
        $supplierList->assertSee('Supplier 01');
        $supplierList->assertSee('Supplier 10');
        $supplierList->assertDontSee('Supplier 11');

        // Supplier search
        $supplierSearch = $this->actingAs($this->staff)->get(route('inventory.suppliers.index', ['search' => 'Supplier 12']));
        $supplierSearch->assertSee('Supplier 12');
        $supplierSearch->assertDontSee('Supplier 01');

        // Product search
        $productSearch = $this->actingAs($this->staff)->get(route('inventory.products.index', ['search' => 'SKU-07']));
        $productSearch->assertSee('Product 07');
        $productSearch->assertDontSee('Product 01');
    }

    public function test_supplier_with_purchases_cannot_be_deleted(): void
    {
        $supplier = Supplier::create(['name' => 'Protected Vendor', 'is_active' => true]);
        GoodsReceivedNote::create([
            'grn_number' => 'GRN-PROT-01',
            'supplier_id' => $supplier->id,
            'received_date' => '2026-10-19',
            'status' => 'confirmed',
            'total_amount' => 500.00,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('inventory.suppliers.destroy', $supplier));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id]);
    }
}
