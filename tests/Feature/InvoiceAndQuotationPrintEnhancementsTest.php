<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Service;
use App\Models\UnitOfMeasurement;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InvoiceAndQuotationPrintEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected Branch $branch;

    protected CompanySetting $company;

    protected Customer $customer;

    protected VehicleType $vehicleType;

    protected Vehicle $vehicle;

    protected Service $service;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = CompanySetting::getSettings();
        $this->company->update([
            'company_name' => 'Viran Auto Care',
            'phone' => '077 620 80 59',
            'address' => 'Mattegoda',
            'email' => 'info@viranautocare.lk',
            'logo_path' => 'company/test_logo.png',
            'signature_path' => null,
        ]);

        $this->branch = Branch::create([
            'company_id' => $this->company->id,
            'branch_code' => 'BR-01',
            'branch_name' => 'Mattegoda Branch',
            'address' => 'Mattegoda',
            'phone' => '077 620 80 59',
            'is_main_branch' => true,
            'is_active' => true,
        ]);

        $this->admin = User::factory()->admin()->create();

        $this->staff = User::factory()->staff()->create();

        $this->customer = Customer::create([
            'customer_name' => 'Kamal Gunaratne',
            'phone' => '0771234567',
            'email' => 'kamal@example.com',
            'address' => 'Mattegoda, Colombo',
            'is_active' => true,
        ]);

        $this->vehicleType = VehicleType::firstOrCreate(
            ['name' => 'Sedan'],
            ['category' => 'car', 'is_active' => true]
        );

        $this->vehicle = Vehicle::create([
            'customer_id' => $this->customer->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'vehicle_number' => 'WP-CAB-1234',
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2020,
            'is_active' => true,
        ]);

        $this->service = Service::create([
            'name' => 'Full Engine Tune Up',
            'code' => 'SRV-TUNE',
            'base_price' => 4500.00,
            'is_active' => true,
        ]);

        $uom = UnitOfMeasurement::firstOrCreate(
            ['code' => 'CAN'],
            ['name' => 'Cans', 'allows_decimal' => false]
        );

        $this->product = Product::create([
            'name' => 'Mobil Super 4L Engine Oil',
            'sku' => 'PRD-OIL-001',
            'uom_id' => $uom->id,
            'selling_price' => 8500.00,
            'purchase_price' => 7000.00,
            'is_active' => true,
        ]);
    }

    /**
     * 1. Test Quotation views have renamed Print button while preserving A4 button and functionality.
     */
    public function test_quotation_views_render_a4_and_print_buttons(): void
    {
        $quotation = Quotation::create([
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'quotation_number' => 'QT-202610-0001',
            'quotation_date' => now()->toDateString(),
            'expiry_date' => now()->addDays(14)->toDateString(),
            'subject' => 'Service Quotation',
            'subtotal' => 13000.00,
            'discount_type' => 'none',
            'discount_value' => 0,
            'discount_amount' => 0,
            'tax_percentage' => 0,
            'tax_amount' => 0,
            'additional_charges' => 0,
            'grand_total' => 13000.00,
            'status' => 'draft',
            'created_by' => $this->admin->id,
        ]);

        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'item_type' => 'service',
            'item_id' => $this->service->id,
            'item_name' => $this->service->name,
            'quantity' => 1,
            'unit_price' => 4500.00,
            'subtotal' => 4500.00,
            'total' => 4500.00,
        ]);

        // Show page: verify [A4] and [Print] buttons
        $showResponse = $this->actingAs($this->staff)->get(route('billing.quotations.show', $quotation));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('A4');
        $showResponse->assertSee('Print');
        $showResponse->assertSee(route('billing.quotations.print', $quotation));

        // Index page: verify Print action link/button
        $indexResponse = $this->actingAs($this->staff)->get(route('billing.quotations.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Print');
        $indexResponse->assertSee(route('billing.quotations.print', $quotation));

        // Verify A4 print route renders without errors
        $printResponse = $this->actingAs($this->staff)->get(route('billing.quotations.print', $quotation));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('QT-202610-0001');
    }

    /**
     * 2. Test Invoice views have renamed Print button while preserving A4 button and functionality.
     */
    public function test_invoice_views_render_a4_and_print_buttons(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-0042',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'subtotal' => 13000.00,
            'grand_total' => 13000.00,
            'amount_paid' => 5000.00,
            'balance_amount' => 8000.00,
            'status' => 'issued',
            'payment_status' => 'partially_paid',
            'created_by' => $this->admin->id,
        ]);

        Payment::create([
            'payment_number' => 'PAY-202610-0042',
            'invoice_id' => $invoice->id,
            'customer_id' => $this->customer->id,
            'branch_id' => $this->branch->id,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'cash',
            'amount' => 5000.00,
            'status' => 'completed',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->staff)->get(route('operations.invoices.show', $invoice));
        $response->assertStatus(200);

        // Header buttons
        $response->assertSee('A4');
        $response->assertSee('Print');
        $response->assertSee(route('operations.invoices.print', $invoice));
        $response->assertSee(route('operations.invoices.thermal', $invoice));

        // Must not contain the old label '80mm' as a standalone button label
        $html = $response->getContent();
        $this->assertStringNotContainsString('>80mm<', $html);
        $this->assertStringNotContainsString('>Print Bill<', $html);

        // Thermal receipt page has Print toolbar button
        $thermalResponse = $this->actingAs($this->staff)->get(route('operations.invoices.thermal', $invoice));
        $thermalResponse->assertStatus(200);
        $thermalResponse->assertSee('Print');
    }

    /**
     * 3. Test 80mm thermal invoice print sequence and company logo rendering.
     */
    public function test_80mm_thermal_invoice_print_sequence_and_company_logo(): void
    {
        Storage::fake('public');
        $logoFile = UploadedFile::fake()->image('viran_logo.png', 160, 60);
        $storedLogo = $logoFile->store('company', 'public');

        $this->company->update([
            'logo_path' => $storedLogo,
            'company_name' => 'VIRAN AUTO CARE',
            'address' => 'Mattegoda',
            'phone' => '077 620 80 59',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-SEQ01',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'subtotal' => 13000.00,
            'discount_amount' => 500.00,
            'tax_amount' => 0.00,
            'grand_total' => 12500.00,
            'amount_paid' => 12500.00,
            'balance_amount' => 0.00,
            'status' => 'issued',
            'payment_status' => 'paid',
            'created_by' => $this->admin->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_type' => 'service',
            'item_name' => 'Full Engine Tune Up',
            'quantity' => 1,
            'unit_price' => 4500.00,
            'subtotal' => 4500.00,
            'total' => 4500.00,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_type' => 'product',
            'item_name' => 'Mobil Super 4L Engine Oil',
            'quantity' => 1,
            'unit_price' => 8500.00,
            'subtotal' => 8500.00,
            'total' => 8500.00,
        ]);

        $response = $this->actingAs($this->staff)->get(route('operations.invoices.thermal', $invoice));
        $response->assertStatus(200);

        $html = $response->getContent();

        // 1. Company logo is displayed above company name with .logo-img
        $this->assertStringContainsString('class="logo-img"', $html);

        // 2. Company name
        $this->assertStringContainsString('VIRAN AUTO CARE', $html);

        // 3. Company address and phone
        $this->assertStringContainsString('Mattegoda', $html);
        $this->assertStringContainsString('077 620 80 59', $html);

        // 4. Invoice title
        $this->assertStringContainsString('SALES INVOICE', $html);

        // 5. Invoice number
        $this->assertStringContainsString('INV-202610-SEQ01', $html);

        // 6. Date & Time
        $this->assertStringContainsString(now()->format('d/m/Y'), $html);

        // 7. Customer & Vehicle
        $this->assertStringContainsString('Kamal Gunaratne', $html);

        // 8. Services and products
        $this->assertStringContainsString('Full Engine Tune Up', $html);
        $this->assertStringContainsString('Mobil Super 4L Engine Oil', $html);

        // 9. Quantity, unit price and amount (headers: Qty, Price, Amount)
        $this->assertStringContainsString('Qty', $html);
        $this->assertStringContainsString('Price', $html);
        $this->assertStringContainsString('Amount', $html);

        // 10. Subtotal
        $this->assertStringContainsString('13,000.00', $html);

        // 11. Discount
        $this->assertStringContainsString('500.00', $html);

        // 12. Grand Total
        $this->assertStringContainsString('12,500.00', $html);

        // 13. Paid Amount
        $this->assertStringContainsString('Paid Amount', $html);

        // 14. Outstanding Balance
        $this->assertStringContainsString('Outstanding Balance:', $html);

        // 15. Payment Status
        $this->assertStringContainsString('Status: PAID', $html);

        // 16. Authorized Signature
        $this->assertStringContainsString('Authorized Signature', $html);

        // 17. Thank-you message
        $this->assertStringContainsString('Thank you for choosing us!', $html);

        // Check exact print sequence in order of appearance in receipt HTML
        $receiptHtml = substr($html, strpos($html, '<div class="thermal-receipt">'));

        $posLogo = strpos($receiptHtml, 'class="logo-img"');
        $posName = strpos($receiptHtml, 'class="company-name">VIRAN AUTO CARE');
        $posAddr = strpos($receiptHtml, 'Mattegoda');
        $posTitle = strpos($receiptHtml, 'SALES INVOICE');
        $posInvNo = strpos($receiptHtml, 'INV-202610-SEQ01');
        $posCustomer = strpos($receiptHtml, 'Kamal Gunaratne');
        $posItems = strpos($receiptHtml, 'Full Engine Tune Up');
        $posSubtotal = strpos($receiptHtml, 'Subtotal:');
        $posTotal = strpos($receiptHtml, 'TOTAL:');
        $posPaid = strpos($receiptHtml, 'Paid Amount:');
        $posStatus = strpos($receiptHtml, 'Status:');
        $posSig = strpos($receiptHtml, 'Authorized Signature');
        $posThanks = strpos($receiptHtml, 'Thank you for choosing us!');

        $this->assertTrue($posLogo < $posName, 'Logo should appear before Company Name');
        $this->assertTrue($posName < $posAddr, 'Company Name should appear before Address');
        $this->assertTrue($posAddr < $posTitle, 'Address should appear before Title');
        $this->assertTrue($posTitle < $posInvNo, 'Title should appear before Invoice Number');
        $this->assertTrue($posInvNo < $posCustomer, 'Invoice Number should appear before Customer info');
        $this->assertTrue($posCustomer < $posItems, 'Customer info should appear before Items');
        $this->assertTrue($posItems < $posSubtotal, 'Items should appear before Subtotal');
        $this->assertTrue($posSubtotal < $posTotal, 'Subtotal should appear before Total');
        $this->assertTrue($posTotal < $posPaid, 'Total should appear before Paid');
        $this->assertTrue($posPaid < $posStatus, 'Paid should appear before Payment Status');
        $this->assertTrue($posStatus < $posSig, 'Payment Status should appear before Signature');
        $this->assertTrue($posSig < $posThanks, 'Signature should appear before Thank-you message');
    }

    /**
     * 4. Test when no company logo is present, only company name is shown without broken image.
     */
    public function test_80mm_thermal_invoice_without_logo_shows_only_company_name(): void
    {
        $this->company->update([
            'logo_path' => null,
            'company_name' => 'VIRAN AUTO CARE',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-NOLOGO',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'subtotal' => 4500.00,
            'grand_total' => 4500.00,
            'amount_paid' => 4500.00,
            'balance_amount' => 0.00,
            'status' => 'issued',
            'payment_status' => 'paid',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->staff)->get(route('operations.invoices.thermal', $invoice));
        $response->assertStatus(200);

        $html = $response->getContent();
        $this->assertStringNotContainsString('class="logo-img"', $html);
        $this->assertStringContainsString('VIRAN AUTO CARE', $html);
    }

    /**
     * 5. Test Authorized Signature display states: blank line vs configured signature image.
     */
    public function test_80mm_thermal_invoice_authorized_signature_states(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-SIG01',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'subtotal' => 5000.00,
            'grand_total' => 5000.00,
            'amount_paid' => 5000.00,
            'balance_amount' => 0.00,
            'status' => 'issued',
            'payment_status' => 'paid',
            'created_by' => $this->admin->id,
        ]);

        // State A: No signature configured -> blank signing line
        $this->company->update(['signature_path' => null]);

        $resBlank = $this->actingAs($this->staff)->get(route('operations.invoices.thermal', $invoice));
        $resBlank->assertStatus(200);
        $resBlank->assertSee('Authorized Signature');
        $resBlank->assertSee('signature-blank-space');
        $resBlank->assertDontSee('class="signature-img"');

        // State B: Signature configured -> signature image above label
        Storage::fake('public');
        $sigFile = UploadedFile::fake()->image('authorized_sig.png', 180, 50);
        $storedSig = $sigFile->store('company/signatures', 'public');
        $this->company->update(['signature_path' => $storedSig]);

        $resImage = $this->actingAs($this->staff)->get(route('operations.invoices.thermal', $invoice));
        $resImage->assertStatus(200);
        $resImage->assertSee('Authorized Signature');
        $resImage->assertSee('class="signature-img"', false);
        $resImage->assertDontSee('class="signature-blank-space"', false);
    }

    /**
     * 6. Test admin can upload, replace, and remove authorized signature via Company Details.
     */
    public function test_admin_can_upload_replace_and_remove_authorized_signature(): void
    {
        Storage::fake('public');

        // Upload initial signature
        $sigFile = UploadedFile::fake()->image('signature1.png', 200, 80);
        $resUpload = $this->actingAs($this->admin)->put(route('master-data.company-details.update'), [
            'company_name' => 'Viran Auto Care Center',
            'signature' => $sigFile,
        ]);

        $resUpload->assertRedirect(route('master-data.company-details.edit'));
        $resUpload->assertSessionHas('success');

        $this->company->refresh();
        $this->assertNotNull($this->company->signature_path);
        Storage::disk('public')->assertExists($this->company->signature_path);
        $firstPath = $this->company->signature_path;

        // View edit page as admin -> see preview and remove checkbox
        $viewEdit = $this->actingAs($this->admin)->get(route('master-data.company-details.edit'));
        $viewEdit->assertStatus(200);
        $viewEdit->assertSee('Authorized Signature');
        $viewEdit->assertSee('remove_signature');

        // Replace with new signature
        $sigFile2 = UploadedFile::fake()->image('signature2.png', 200, 80);
        $resReplace = $this->actingAs($this->admin)->put(route('master-data.company-details.update'), [
            'company_name' => 'Viran Auto Care Center',
            'signature' => $sigFile2,
        ]);
        $resReplace->assertRedirect(route('master-data.company-details.edit'));

        $this->company->refresh();
        $this->assertNotEquals($firstPath, $this->company->signature_path);
        Storage::disk('public')->assertExists($this->company->signature_path);
        Storage::disk('public')->assertMissing($firstPath);

        // Remove signature
        $resRemove = $this->actingAs($this->admin)->put(route('master-data.company-details.update'), [
            'company_name' => 'Viran Auto Care Center',
            'remove_signature' => '1',
        ]);
        $resRemove->assertRedirect(route('master-data.company-details.edit'));

        $this->company->refresh();
        $this->assertNull($this->company->signature_path);
    }

    /**
     * 7. Test staff users cannot upload or modify company authorized signature.
     */
    public function test_staff_cannot_upload_or_modify_authorized_signature(): void
    {
        Storage::fake('public');
        $sigFile = UploadedFile::fake()->image('signature.png', 200, 80);

        $response = $this->actingAs($this->staff)->put(route('master-data.company-details.update'), [
            'company_name' => 'Unauthorized Change',
            'signature' => $sigFile,
        ]);

        $response->assertStatus(403);
    }

    /**
     * 8. Test 80mm thermal receipt handles multiple products, partially paid and unpaid invoices.
     */
    public function test_80mm_thermal_invoice_handles_multiple_products_and_payment_states(): void
    {
        // Partially paid invoice with multiple items
        $invoicePartiallyPaid = Invoice::create([
            'invoice_number' => 'INV-202610-MULTI01',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'subtotal' => 25000.00,
            'discount_amount' => 1000.00,
            'grand_total' => 24000.00,
            'amount_paid' => 10000.00,
            'balance_amount' => 14000.00,
            'status' => 'issued',
            'payment_status' => 'partially_paid',
            'created_by' => $this->admin->id,
        ]);

        for ($i = 1; $i <= 3; $i++) {
            InvoiceItem::create([
                'invoice_id' => $invoicePartiallyPaid->id,
                'item_type' => 'product',
                'item_name' => "Replacement Filter Part #{$i}",
                'quantity' => $i,
                'unit_price' => 2500.00,
                'subtotal' => 2500.00 * $i,
                'total' => 2500.00 * $i,
            ]);
        }

        $response = $this->actingAs($this->staff)->get(route('operations.invoices.thermal', $invoicePartiallyPaid));
        $response->assertStatus(200);

        // Verify multi-item table
        $response->assertSee('Replacement Filter Part #1');
        $response->assertSee('Replacement Filter Part #2');
        $response->assertSee('Replacement Filter Part #3');

        // Verify partial payment details
        $response->assertSee('PARTIALLY PAID');
        $response->assertSee('10,000.00');
        $response->assertSee('14,000.00');

        // Check CSS contains 80mm page definition
        $html = $response->getContent();
        $this->assertStringContainsString('size: 80mm auto', $html);
        $this->assertStringContainsString('width: 76mm', $html);

        // Unpaid invoice
        $invoiceUnpaid = Invoice::create([
            'invoice_number' => 'INV-202610-UNPAID',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'subtotal' => 5000.00,
            'grand_total' => 5000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 5000.00,
            'status' => 'issued',
            'payment_status' => 'unpaid',
            'created_by' => $this->admin->id,
        ]);

        $resUnpaid = $this->actingAs($this->staff)->get(route('operations.invoices.thermal', $invoiceUnpaid));
        $resUnpaid->assertStatus(200);
        $resUnpaid->assertSee('UNPAID');
        $resUnpaid->assertSee('5,000.00');
    }

    /**
     * 9. Test A4 invoice printing remains unchanged and neither print generates new invoice numbers.
     */
    public function test_a4_printing_remains_unchanged_and_does_not_mutate_invoice_number(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-ORIGINAL',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'subtotal' => 6000.00,
            'grand_total' => 6000.00,
            'amount_paid' => 6000.00,
            'balance_amount' => 0.00,
            'status' => 'issued',
            'payment_status' => 'paid',
            'created_by' => $this->admin->id,
        ]);

        $initialCount = Invoice::count();

        // Print A4
        $resA4 = $this->actingAs($this->staff)->get(route('operations.invoices.print', $invoice));
        $resA4->assertStatus(200);
        $resA4->assertSee('INV-202610-ORIGINAL');

        // Print Thermal
        $resThermal = $this->actingAs($this->staff)->get(route('operations.invoices.thermal', $invoice));
        $resThermal->assertStatus(200);
        $resThermal->assertSee('INV-202610-ORIGINAL');

        // Invoice count unchanged & invoice number untouched
        $this->assertEquals($initialCount, Invoice::count());
        $this->assertEquals('INV-202610-ORIGINAL', $invoice->fresh()->invoice_number);
    }
}
