<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\UnitOfMeasurement;
use App\Models\User;
use App\Services\Inventory\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierPaymentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected Supplier $supplier;

    protected UnitOfMeasurement $uom;

    protected Product $product;

    protected GoodsReceivedNote $grn;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->staff = User::factory()->staff()->create();

        $this->uom = UnitOfMeasurement::firstOrCreate(
            ['name' => 'Can'],
            ['code' => 'CAN', 'is_active' => true]
        );

        $this->supplier = Supplier::create([
            'name' => 'AutoCare Lubricants Ltd',
            'registration_number' => 'PV-100200',
            'mobile' => '0779998877',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'name' => 'Engine Oil 5W-30 Synthetic',
            'sku' => 'OIL-5W30-SYN',
            'uom_id' => $this->uom->id,
            'selling_price' => 7500.00,
            'is_active' => true,
        ]);

        // Create a confirmed GRN with total amount LKR 147,000
        $this->grn = GoodsReceivedNote::create([
            'grn_number' => 'GRN-202610-0099',
            'supplier_id' => $this->supplier->id,
            'supplier_invoice_number' => 'INV-SUP-991',
            'received_date' => now()->toDateString(),
            'status' => 'draft',
            'total_amount' => 147000.00,
            'created_by' => $this->admin->id,
        ]);

        GoodsReceivedNoteItem::create([
            'grn_id' => $this->grn->id,
            'product_id' => $this->product->id,
            'purchase_quantity' => 20,
            'free_quantity' => 0,
            'purchase_unit_price' => 7350.00,
            'line_total' => 147000.00,
        ]);

        // Confirm the GRN so inventory batches are created
        app(StockService::class)->confirmGrn($this->grn, $this->admin);
        $this->grn->refresh();
    }

    public function test_initial_grn_state_is_unpaid(): void
    {
        $this->assertEquals(147000.00, (float) $this->grn->total_amount);
        $this->assertEquals(0.00, $this->grn->paid_amount);
        $this->assertEquals(147000.00, $this->grn->outstanding_amount);
        $this->assertEquals('Unpaid', $this->grn->payment_status);
    }

    public function test_admin_can_record_partial_supplier_payment_and_status_becomes_partially_paid(): void
    {
        $response = $this->actingAs($this->admin)->post(route('inventory.grn.payments.store', $this->grn), [
            'payment_date' => now()->toDateString(),
            'amount' => 50000.00,
            'payment_method' => 'bank_transfer',
            'reference_number' => 'BOC-TXN-44910',
            'notes' => 'First partial advance to supplier',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->grn->refresh();
        $this->assertEquals(50000.00, $this->grn->paid_amount);
        $this->assertEquals(97000.00, $this->grn->outstanding_amount);
        $this->assertEquals('Partially Paid', $this->grn->payment_status);

        $this->assertDatabaseHas('supplier_payments', [
            'grn_id' => $this->grn->id,
            'supplier_id' => $this->supplier->id,
            'amount' => 50000.00,
            'payment_method' => 'bank_transfer',
            'reference_number' => 'BOC-TXN-44910',
            'status' => 'completed',
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_admin_can_record_second_payment_to_reach_fully_paid_status(): void
    {
        // First payment: LKR 50,000
        $this->actingAs($this->admin)->post(route('inventory.grn.payments.store', $this->grn), [
            'payment_date' => now()->toDateString(),
            'amount' => 50000.00,
            'payment_method' => 'bank_transfer',
        ]);

        // Second payment: LKR 97,000
        $response = $this->actingAs($this->admin)->post(route('inventory.grn.payments.store', $this->grn), [
            'payment_date' => now()->toDateString(),
            'amount' => 97000.00,
            'payment_method' => 'cash',
            'notes' => 'Settlement in full',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->grn->refresh();
        $this->assertEquals(147000.00, $this->grn->paid_amount);
        $this->assertEquals(0.00, $this->grn->outstanding_amount);
        $this->assertEquals('Paid', $this->grn->payment_status);
        $this->assertCount(2, $this->grn->payments);
    }

    public function test_cannot_overpay_grn_beyond_outstanding_balance(): void
    {
        // Outstanding is 147,000. Try to pay 150,000.
        $response = $this->actingAs($this->admin)->post(route('inventory.grn.payments.store', $this->grn), [
            'payment_date' => now()->toDateString(),
            'amount' => 150000.00,
            'payment_method' => 'cash',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->grn->refresh();
        $this->assertEquals(0.00, $this->grn->paid_amount);
        $this->assertEquals(147000.00, $this->grn->outstanding_amount);
        $this->assertEquals('Unpaid', $this->grn->payment_status);
        $this->assertCount(0, $this->grn->payments);
    }

    public function test_cannot_record_payment_on_already_fully_paid_grn(): void
    {
        // Fully pay
        $this->actingAs($this->admin)->post(route('inventory.grn.payments.store', $this->grn), [
            'payment_date' => now()->toDateString(),
            'amount' => 147000.00,
            'payment_method' => 'bank_transfer',
        ]);

        $this->grn->refresh();
        $this->assertEquals('Paid', $this->grn->payment_status);

        // Try to pay again
        $response = $this->actingAs($this->admin)->post(route('inventory.grn.payments.store', $this->grn), [
            'payment_date' => now()->toDateString(),
            'amount' => 1000.00,
            'payment_method' => 'cash',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_recording_payment_does_not_modify_inventory_batches_or_stock_quantities(): void
    {
        $batch = StockBatch::where('grn_id', $this->grn->id)->first();
        $initialBatchQty = $batch->received_quantity;
        $initialRemainingQty = $batch->remaining_quantity;
        $initialUnitCost = $batch->inventory_unit_cost;
        $initialProductSellingPrice = $this->product->fresh()->selling_price;
        $initialBatchesCount = StockBatch::count();

        // Record payment
        $this->actingAs($this->admin)->post(route('inventory.grn.payments.store', $this->grn), [
            'payment_date' => now()->toDateString(),
            'amount' => 50000.00,
            'payment_method' => 'cheque',
            'reference_number' => 'CHQ-9901',
        ]);

        // Verify stock batch invariants
        $batch->refresh();
        $this->assertEquals($initialBatchQty, $batch->received_quantity);
        $this->assertEquals($initialRemainingQty, $batch->remaining_quantity);
        $this->assertEquals($initialUnitCost, $batch->inventory_unit_cost);
        $this->assertEquals($initialBatchesCount, StockBatch::count());
        $this->assertEquals($initialProductSellingPrice, $this->product->fresh()->selling_price);
    }

    public function test_admin_can_reverse_payment_and_outstanding_balance_is_restored(): void
    {
        // Record payment
        $this->actingAs($this->admin)->post(route('inventory.grn.payments.store', $this->grn), [
            'payment_date' => now()->toDateString(),
            'amount' => 60000.00,
            'payment_method' => 'card',
        ]);

        $payment = SupplierPayment::where('grn_id', $this->grn->id)->firstOrFail();
        $this->assertEquals(60000.00, $this->grn->fresh()->paid_amount);
        $this->assertEquals(87000.00, $this->grn->fresh()->outstanding_amount);

        // Reverse the payment
        $response = $this->actingAs($this->admin)->post(route('inventory.supplier-payments.reverse', $payment), [
            'reversal_reason' => 'Cheque was stopped due to incorrect vendor bank details',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $payment->refresh();
        $this->assertTrue($payment->isReversed());
        $this->assertEquals('reversed', $payment->status);
        $this->assertEquals('Cheque was stopped due to incorrect vendor bank details', $payment->reversal_reason);
        $this->assertEquals($this->admin->id, $payment->reversed_by);
        $this->assertNotNull($payment->reversed_at);

        // GRN paid amount and status must restore
        $this->grn->refresh();
        $this->assertEquals(0.00, $this->grn->paid_amount);
        $this->assertEquals(147000.00, $this->grn->outstanding_amount);
        $this->assertEquals('Unpaid', $this->grn->payment_status);
    }

    public function test_staff_cannot_reverse_supplier_payment(): void
    {
        // Record payment as admin
        $this->actingAs($this->admin)->post(route('inventory.grn.payments.store', $this->grn), [
            'payment_date' => now()->toDateString(),
            'amount' => 30000.00,
            'payment_method' => 'cash',
        ]);

        $payment = SupplierPayment::where('grn_id', $this->grn->id)->firstOrFail();

        // Staff tries to reverse
        $response = $this->actingAs($this->staff)->post(route('inventory.supplier-payments.reverse', $payment), [
            'reversal_reason' => 'Unauthorized attempt',
        ]);

        $response->assertStatus(403);
        $this->assertFalse($payment->fresh()->isReversed());
    }

    public function test_viewing_and_printing_payment_receipt(): void
    {
        $this->actingAs($this->admin)->post(route('inventory.grn.payments.store', $this->grn), [
            'payment_date' => now()->toDateString(),
            'amount' => 50000.00,
            'payment_method' => 'bank_transfer',
            'reference_number' => 'SLIP-771',
        ]);

        $payment = SupplierPayment::where('grn_id', $this->grn->id)->firstOrFail();

        $response = $this->actingAs($this->admin)->get(route('inventory.supplier-payments.receipt', $payment));
        $response->assertStatus(200);
        $response->assertSee('Supplier Payment Voucher');
        $response->assertSee($payment->payment_number);
        $response->assertSee('50,000.00');
        $response->assertSee($this->supplier->name);
        $response->assertSee($this->grn->grn_number);
    }

    public function test_grn_list_and_detail_views_display_correct_payment_buttons(): void
    {
        // Unpaid GRN shows 'Record Payment'
        $listResponse = $this->actingAs($this->admin)->get(route('inventory.grn.index'));
        $listResponse->assertStatus(200);
        $listResponse->assertSee('Record Payment');
        $listResponse->assertSee('Unpaid');

        $showResponse = $this->actingAs($this->admin)->get(route('inventory.grn.show', $this->grn));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Record Payment');
        $showResponse->assertSee('Save Payment');

        // Fully pay
        $this->actingAs($this->admin)->post(route('inventory.grn.payments.store', $this->grn), [
            'payment_date' => now()->toDateString(),
            'amount' => 147000.00,
            'payment_method' => 'bank_transfer',
        ]);

        // Fully paid GRN shows 'View Payments'
        $paidListResponse = $this->actingAs($this->admin)->get(route('inventory.grn.index'));
        $paidListResponse->assertStatus(200);
        $paidListResponse->assertSee('View Payments');
        $paidListResponse->assertSee('Paid');
    }

    public function test_branch_access_is_enforced_when_recording_payment(): void
    {
        $company = CompanySetting::getSettings();

        $branch1 = Branch::create([
            'company_id' => $company->id,
            'branch_name' => 'Kandy Branch',
            'branch_code' => 'KDY',
            'phone' => '0812233445',
            'is_main_branch' => false,
            'is_active' => true,
        ]);

        $branch2 = Branch::create([
            'company_id' => $company->id,
            'branch_name' => 'Galle Branch',
            'branch_code' => 'GLE',
            'phone' => '0912233445',
            'is_main_branch' => false,
            'is_active' => true,
        ]);

        // Assign staff to branch 1 only
        $this->staff->branches()->attach($branch1->id);

        // Create GRN belonging to branch 2
        $branch2Grn = GoodsReceivedNote::create([
            'grn_number' => 'GRN-202610-0088',
            'supplier_id' => $this->supplier->id,
            'branch_id' => $branch2->id,
            'received_date' => now()->toDateString(),
            'status' => 'confirmed',
            'total_amount' => 10000.00,
            'created_by' => $this->admin->id,
        ]);

        // Staff assigned to branch 1 cannot record payment for branch 2's GRN
        $response = $this->actingAs($this->staff)->post(route('inventory.grn.payments.store', $branch2Grn), [
            'payment_date' => now()->toDateString(),
            'amount' => 5000.00,
            'payment_method' => 'cash',
        ]);

        $response->assertStatus(403);
    }
}
