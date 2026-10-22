<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JobCard;
use App\Models\Payment;
use App\Models\PrinterSetting;
use App\Models\PrintLog;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Services\PrintService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwoPrinterAutomaticRoutingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected Branch $mainBranch;
    protected Branch $secondaryBranch;
    protected CompanySetting $company;
    protected PrintService $printService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = CompanySetting::getSettings();
        $this->company->update([
            'company_name' => 'Viran Auto Care Center',
            'phone' => '011-2345678',
            'address' => '123 Mattegoda Road, Colombo',
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@autocare.com',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->staff = User::factory()->create([
            'name' => 'Cashier Staff',
            'email' => 'cashier@autocare.com',
            'role' => 'staff',
            'status' => 'active',
        ]);

        $this->mainBranch = Branch::create([
            'company_id' => $this->company->id,
            'branch_code' => 'BR-01',
            'branch_name' => 'Mattegoda Main Center',
            'address' => '123 Mattegoda, Colombo',
            'phone' => '011-2345678',
            'is_main_branch' => true,
            'is_active' => true,
        ]);

        $this->secondaryBranch = Branch::create([
            'company_id' => $this->company->id,
            'branch_code' => 'BR-02',
            'branch_name' => 'Kottawa Branch',
            'address' => '456 Kottawa Junction',
            'phone' => '011-8765432',
            'is_main_branch' => false,
            'is_active' => true,
        ]);

        $this->printService = app(PrintService::class);
    }

    /**
     * 1. Test public digital certificate & SHA-512 signing endpoints for QZ Tray integration.
     */
    public function test_qz_tray_certificate_and_signing_endpoints(): void
    {
        // Certificate endpoint accessible by authenticated staff/admin
        $certResponse = $this->actingAs($this->staff)->get(route('print.certificate'));
        $certResponse->assertStatus(200);
        $certResponse->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $this->assertStringContainsString('BEGIN CERTIFICATE', $certResponse->getContent());

        // Signing endpoint signs payload securely with SHA-512
        $toSign = 'sample-qz-print-data-hash-to-sign';
        $signResponse = $this->actingAs($this->staff)->postJson(route('print.sign'), [
            'request' => $toSign,
        ]);

        $signResponse->assertStatus(200);
        $signResponse->assertJsonStructure(['signature']);
        $this->assertNotEmpty($signResponse->json('signature'));
    }

    /**
     * 2. Test saving and updating Printer 1 (Bill) and Printer 2 (Job Card) assignments.
     */
    public function test_save_and_update_two_printer_assignments(): void
    {
        $payload = [
            'branch_id' => $this->mainBranch->id,
            'workstation_name' => 'FrontDesk-Cashier',
            'bill_printer' => 'EPSON TM-T88VI Thermal',
            'job_card_printer' => 'Xprinter XP-80C Thermal',
            'paper_size' => '80mm',
            'auto_route' => true,
            'notes' => 'Dual thermal printer setup on cashier terminal',
        ];

        // Admin saves mapping
        $response = $this->actingAs($this->admin)->post(route('admin.printer-settings.store'), $payload);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('printer_settings', [
            'branch_id' => $this->mainBranch->id,
            'workstation_name' => 'FrontDesk-Cashier',
            'bill_printer' => 'EPSON TM-T88VI Thermal',
            'job_card_printer' => 'Xprinter XP-80C Thermal',
            'paper_size' => '80mm',
            'auto_route' => true,
        ]);

        $setting = PrinterSetting::where('workstation_name', 'FrontDesk-Cashier')->first();
        $this->assertNotNull($setting);

        // Update printer mapping later
        $updatePayload = array_merge($payload, [
            'bill_printer' => 'Bixolon SRP-350III',
        ]);
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.printer-settings.update', $setting), $updatePayload);
        $updateResponse->assertRedirect();

        $this->assertDatabaseHas('printer_settings', [
            'id' => $setting->id,
            'bill_printer' => 'Bixolon SRP-350III',
        ]);
    }

    /**
     * 3, 4, 5. Test automatic routing for Bill, 80mm Invoice, and Payment Receipt to Printer 1 (Bill Printer).
     */
    public function test_automatic_routing_for_bill_printer_documents(): void
    {
        PrinterSetting::create([
            'branch_id' => $this->mainBranch->id,
            'workstation_name' => 'Counter-01',
            'bill_printer' => 'Bill-Thermal-80mm',
            'job_card_printer' => 'JobCard-Thermal-80mm',
            'paper_size' => '80mm',
            'auto_route' => true,
        ]);

        // Bill Document -> routes to Printer 1
        $billRoute = $this->printService->resolvePrinter('bill', $this->mainBranch->id, 'Counter-01');
        $this->assertTrue($billRoute['configured']);
        $this->assertEquals('Bill-Thermal-80mm', $billRoute['target_printer']);
        $this->assertEquals('bill', $billRoute['printer_role']);

        // 80mm Sales Invoice -> routes to Printer 1
        $invoiceRoute = $this->printService->resolvePrinter('invoice_80mm', $this->mainBranch->id, 'Counter-01');
        $this->assertEquals('Bill-Thermal-80mm', $invoiceRoute['target_printer']);
        $this->assertEquals('bill', $invoiceRoute['printer_role']);

        // Payment Receipt -> routes to Printer 1
        $paymentRoute = $this->printService->resolvePrinter('payment_receipt', $this->mainBranch->id, 'Counter-01');
        $this->assertEquals('Bill-Thermal-80mm', $paymentRoute['target_printer']);
        $this->assertEquals('bill', $paymentRoute['printer_role']);

        // Direct Sale POS -> routes to Printer 1
        $posRoute = $this->printService->resolvePrinter('direct_sale', $this->mainBranch->id, 'Counter-01');
        $this->assertEquals('Bill-Thermal-80mm', $posRoute['target_printer']);
    }

    /**
     * 6, 7. Test automatic routing for Full Job Card and Selected Items to Printer 2 (Job Card Printer).
     */
    public function test_automatic_routing_for_job_card_printer_documents(): void
    {
        PrinterSetting::create([
            'branch_id' => $this->mainBranch->id,
            'workstation_name' => 'Workshop-PC',
            'bill_printer' => 'FrontDesk-Bill-80',
            'job_card_printer' => 'Workshop-JobCard-80',
            'paper_size' => '80mm',
            'auto_route' => true,
        ]);

        // Full Job Card -> routes to Printer 2
        $jobRoute = $this->printService->resolvePrinter('job_card', $this->mainBranch->id, 'Workshop-PC');
        $this->assertTrue($jobRoute['configured']);
        $this->assertEquals('Workshop-JobCard-80', $jobRoute['target_printer']);
        $this->assertEquals('job_card', $jobRoute['printer_role']);

        // Selected Items -> routes to Printer 2
        $selectedRoute = $this->printService->resolvePrinter('selected_items', $this->mainBranch->id, 'Workshop-PC');
        $this->assertTrue($selectedRoute['configured']);
        $this->assertEquals('Workshop-JobCard-80', $selectedRoute['target_printer']);
        $this->assertEquals('job_card', $selectedRoute['printer_role']);

        // Workshop Instructions -> routes to Printer 2
        $instructionRoute = $this->printService->resolvePrinter('workshop_instructions', $this->mainBranch->id, 'Workshop-PC');
        $this->assertEquals('Workshop-JobCard-80', $instructionRoute['target_printer']);
    }

    /**
     * 8. Test logging and recording print submissions (audit trail).
     */
    public function test_print_status_logging_and_audit_trail(): void
    {
        $logResponse = $this->actingAs($this->staff)->postJson(route('print.log'), [
            'document_type' => 'bill',
            'document_id' => 101,
            'document_number' => 'INV-2026-00101',
            'printer_name' => 'EPSON TM-T88VI',
            'workstation_name' => 'Cashier-01',
            'branch_id' => $this->mainBranch->id,
            'status' => 'printed',
        ]);

        $logResponse->assertStatus(200);
        $logResponse->assertJson(['success' => true]);

        $this->assertDatabaseHas('print_logs', [
            'document_type' => 'bill',
            'document_number' => 'INV-2026-00101',
            'printer_name' => 'EPSON TM-T88VI',
            'status' => 'printed',
            'user_id' => $this->staff->id,
        ]);
    }

    /**
     * 9. Test disconnected / unassigned printer handling.
     */
    public function test_unassigned_printer_error_message(): void
    {
        // Workstation with no bill printer set
        PrinterSetting::create([
            'branch_id' => $this->mainBranch->id,
            'workstation_name' => 'NoPrinter-PC',
            'bill_printer' => null,
            'job_card_printer' => null,
            'auto_route' => true,
        ]);

        $route = $this->printService->resolvePrinter('bill', $this->mainBranch->id, 'NoPrinter-PC');
        $this->assertFalse($route['configured']);
        $this->assertNull($route['target_printer']);
        $this->assertEquals('Bill Printer is not connected.', $route['message']);

        $jobRoute = $this->printService->resolvePrinter('job_card', $this->mainBranch->id, 'NoPrinter-PC');
        $this->assertFalse($jobRoute['configured']);
        $this->assertEquals('Job Card Printer is not connected.', $jobRoute['message']);
    }

    /**
     * 10. Test reprinting existing invoices and job cards does not duplicate records or alter financials.
     */
    public function test_reprinting_does_not_duplicate_records_or_alter_financials(): void
    {
        $customer = Customer::create([
            'customer_name' => 'Kamal Perera',
            'phone' => '0771234567',
        ]);

        $invoice = Invoice::create([
            'customer_id' => $customer->id,
            'branch_id' => $this->mainBranch->id,
            'invoice_number' => 'INV-2026-0099',
            'invoice_date' => now()->toDateString(),
            'subtotal' => 5000.00,
            'tax_amount' => 0.00,
            'discount_amount' => 0.00,
            'grand_total' => 5000.00,
            'amount_paid' => 5000.00,
            'balance_amount' => 0.00,
            'status' => 'paid',
            'created_by' => $this->admin->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_type' => 'service',
            'item_name' => 'Full Synthetic Oil Change',
            'quantity' => 1,
            'unit_price' => 5000.00,
            'line_total' => 5000.00,
        ]);

        $initialInvoiceCount = Invoice::count();
        $initialItemCount = InvoiceItem::count();
        $initialGrandTotal = (float) $invoice->fresh()->grand_total;
        $initialPaid = (float) $invoice->fresh()->amount_paid;

        // Perform 3 consecutive print requests of the thermal bill
        for ($i = 0; $i < 3; $i++) {
            $response = $this->actingAs($this->admin)->get(route('operations.invoices.thermal', $invoice));
            $response->assertStatus(200);
            $response->assertSee('INV-2026-0099');
            $response->assertSee('5,000.00');
        }

        // Database records & financial amounts MUST NOT CHANGE
        $this->assertEquals($initialInvoiceCount, Invoice::count());
        $this->assertEquals($initialItemCount, InvoiceItem::count());
        $this->assertEquals($initialGrandTotal, (float) $invoice->fresh()->grand_total);
        $this->assertEquals($initialPaid, (float) $invoice->fresh()->amount_paid);
    }

    /**
     * 11. Test 80mm paper layout and actual company details in thermal bill view.
     */
    public function test_thermal_bill_view_contains_80mm_layout_and_company_details(): void
    {
        $customer = Customer::create([
            'customer_name' => 'Nimal Silva',
            'phone' => '0719876543',
        ]);

        $invoice = Invoice::create([
            'customer_id' => $customer->id,
            'branch_id' => $this->mainBranch->id,
            'invoice_number' => 'INV-2026-0042',
            'invoice_date' => now()->toDateString(),
            'subtotal' => 3500.00,
            'grand_total' => 3500.00,
            'amount_paid' => 3500.00,
            'balance_amount' => 0.00,
            'status' => 'paid',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_type' => 'product',
            'item_name' => 'Bosch Oil Filter',
            'quantity' => 1,
            'unit_price' => 3500.00,
            'line_total' => 3500.00,
        ]);

        $response = $this->actingAs($this->staff)->get(route('operations.invoices.thermal', $invoice));
        $response->assertStatus(200);

        // 80mm thermal CSS constraints
        $response->assertSee('width: 80mm', false);
        $response->assertSee('thermal-receipt', false);
        $response->assertSee('AutoCarePrinter', false);

        // Actual Company Details from DB
        $response->assertSee('VIRAN AUTO CARE');
        $response->assertSee($this->mainBranch->phone);
        $response->assertSee('INV-2026-0042');
        $response->assertSee('Nimal Silva');
        $response->assertSee('Bosch Oil Filter');
        $response->assertSee('PAID IN FULL');
    }

    /**
     * 12. Test branch and workstation isolation (settings do not leak between workstations or branches).
     */
    public function test_branch_and_workstation_printer_settings_isolation(): void
    {
        // Branch 1 - Workstation A
        PrinterSetting::create([
            'branch_id' => $this->mainBranch->id,
            'workstation_name' => 'Branch1-Cashier',
            'bill_printer' => 'B1-Epson-Bill',
            'job_card_printer' => 'B1-Xprinter-Job',
            'auto_route' => true,
        ]);

        // Branch 2 - Workstation B
        PrinterSetting::create([
            'branch_id' => $this->secondaryBranch->id,
            'workstation_name' => 'Branch2-Front',
            'bill_printer' => 'B2-Star-Bill',
            'job_card_printer' => 'B2-Citizen-Job',
            'auto_route' => true,
        ]);

        // Fetch Branch 1 configuration
        $config1 = $this->printService->resolvePrinter('bill', $this->mainBranch->id, 'Branch1-Cashier');
        $this->assertEquals('B1-Epson-Bill', $config1['target_printer']);

        // Fetch Branch 2 configuration
        $config2 = $this->printService->resolvePrinter('bill', $this->secondaryBranch->id, 'Branch2-Front');
        $this->assertEquals('B2-Star-Bill', $config2['target_printer']);

        // Active config endpoint tests isolation via query parameters
        $apiResponse = $this->actingAs($this->staff)->getJson(route('print.config', [
            'branch_id' => $this->secondaryBranch->id,
            'workstation' => 'Branch2-Front',
        ]));

        $apiResponse->assertStatus(200);
        $apiResponse->assertJson([
            'bill_printer' => 'B2-Star-Bill',
            'job_card_printer' => 'B2-Citizen-Job',
        ]);
    }
}
