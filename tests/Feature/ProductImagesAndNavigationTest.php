<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\UnitOfMeasurement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImagesAndNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected UnitOfMeasurement $uom;

    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->staff = User::factory()->staff()->create();

        $this->uom = UnitOfMeasurement::firstOrCreate(
            ['name' => 'Piece'],
            ['code' => 'PCS', 'is_active' => true]
        );

        $this->supplier = Supplier::create([
            'name' => 'Auto Parts Lanka',
            'mobile' => '0771234567',
            'is_active' => true,
        ]);
    }

    /**
     * 1. Add product with valid image upload.
     */
    public function test_admin_can_add_product_with_image(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('wiper-blade.png', 600, 600)->size(500);

        $response = $this->actingAs($this->admin)->post(route('inventory.products.store'), [
            'name' => 'Bosch Aerotwin Wiper Blade Set',
            'sku' => 'PRD-BOS-011',
            'description' => 'Premium aerodynamic wiper blade set.',
            'uom_id' => $this->uom->id,
            'preferred_supplier_id' => $this->supplier->id,
            'selling_price' => 4800.00,
            'min_stock' => 5,
            'max_stock' => 50,
            'is_active' => '1',
            'image' => $image,
        ]);

        $response->assertRedirect(route('inventory.products.index'));
        $response->assertSessionHas('success');

        $product = Product::where('sku', 'PRD-BOS-011')->first();
        $this->assertNotNull($product);
        $this->assertNotNull($product->image_path);
        $this->assertTrue(Storage::disk('public')->exists($product->image_path));
        $this->assertStringContainsString('storage/products/', $product->image_url);
    }

    /**
     * 2. Add product without image.
     */
    public function test_admin_can_add_product_without_image(): void
    {
        $response = $this->actingAs($this->admin)->post(route('inventory.products.store'), [
            'name' => 'Standard Oil Filter C111',
            'sku' => 'OF-C111-STD',
            'uom_id' => $this->uom->id,
            'selling_price' => 1250.00,
            'min_stock' => 10,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('inventory.products.index'));

        $product = Product::where('sku', 'OF-C111-STD')->first();
        $this->assertNotNull($product);
        $this->assertNull($product->image_path);
        $this->assertNull($product->image_url);
        $this->assertFalse($product->hasImage());
    }

    /**
     * 3. Edit product without changing its image preserves existing image.
     */
    public function test_editing_product_preserves_existing_image(): void
    {
        Storage::fake('public');

        $storedPath = UploadedFile::fake()->image('original.jpg')->store('products', 'public');

        $product = Product::create([
            'name' => 'Castrol Edge 5W-40 4L',
            'sku' => 'OIL-CAS-5W40',
            'uom_id' => $this->uom->id,
            'selling_price' => 18500.00,
            'image_path' => $storedPath,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('inventory.products.update', $product), [
            'name' => 'Castrol Edge 5W-40 4L Advanced',
            'sku' => 'OIL-CAS-5W40',
            'uom_id' => $this->uom->id,
            'selling_price' => 19200.00,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('inventory.products.index'));

        $product->refresh();
        $this->assertEquals('Castrol Edge 5W-40 4L Advanced', $product->name);
        $this->assertEquals(19200.00, (float) $product->selling_price);
        $this->assertEquals($storedPath, $product->image_path);
        $this->assertTrue(Storage::disk('public')->exists($storedPath));
    }

    /**
     * 4. Replace product image deletes old file after successful database update.
     */
    public function test_replacing_product_image_updates_db_and_deletes_old_file(): void
    {
        Storage::fake('public');

        $oldPath = UploadedFile::fake()->image('old-image.jpg')->store('products', 'public');

        $product = Product::create([
            'name' => 'Brembo Front Brake Pad Set',
            'sku' => 'BRK-BRE-001',
            'uom_id' => $this->uom->id,
            'selling_price' => 14500.00,
            'image_path' => $oldPath,
            'is_active' => true,
        ]);

        $newImage = UploadedFile::fake()->image('new-image.png', 800, 800)->size(600);

        $response = $this->actingAs($this->admin)->put(route('inventory.products.update', $product), [
            'name' => 'Brembo Front Brake Pad Set Ceramic',
            'sku' => 'BRK-BRE-001',
            'uom_id' => $this->uom->id,
            'selling_price' => 16000.00,
            'is_active' => '1',
            'image' => $newImage,
        ]);

        $response->assertRedirect(route('inventory.products.index'));

        $product->refresh();
        $this->assertNotEquals($oldPath, $product->image_path);
        $this->assertTrue(Storage::disk('public')->exists($product->image_path));
        // Old image file must be deleted from storage
        $this->assertFalse(Storage::disk('public')->exists($oldPath));
    }

    /**
     * 5. Validate unsupported file formats are rejected.
     */
    public function test_image_validation_rejects_unsupported_file_types(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('specs.pdf', 300, 'application/pdf');

        $response = $this->actingAs($this->admin)->post(route('inventory.products.store'), [
            'name' => 'Invalid File Product',
            'uom_id' => $this->uom->id,
            'selling_price' => 1000.00,
            'image' => $file,
        ]);

        $response->assertSessionHasErrors('image');
    }

    /**
     * 6. Validate files exceeding 2MB are rejected.
     */
    public function test_image_validation_rejects_oversized_images(): void
    {
        Storage::fake('public');

        $hugeImage = UploadedFile::fake()->image('huge.jpg')->size(2500); // 2.5 MB

        $response = $this->actingAs($this->admin)->post(route('inventory.products.store'), [
            'name' => 'Oversized Image Product',
            'uom_id' => $this->uom->id,
            'selling_price' => 1000.00,
            'image' => $hugeImage,
        ]);

        $response->assertSessionHasErrors('image');
    }

    /**
     * 7. Verify product list displays thumbnails and placeholders.
     */
    public function test_product_list_displays_thumbnail_and_placeholder(): void
    {
        Storage::fake('public');

        $imgPath = UploadedFile::fake()->image('plug.png')->store('products', 'public');

        $prodWithImg = Product::create([
            'name' => 'NGK Iridium Spark Plug',
            'sku' => 'SPK-NGK-01',
            'uom_id' => $this->uom->id,
            'selling_price' => 3200.00,
            'image_path' => $imgPath,
            'is_active' => true,
        ]);

        $prodNoImg = Product::create([
            'name' => 'Generic Washer Fluid 1L',
            'sku' => 'FLU-WSH-01',
            'uom_id' => $this->uom->id,
            'selling_price' => 450.00,
            'image_path' => null,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('inventory.products.index'));
        $response->assertOk();

        $content = $response->getContent();
        // Product with image renders img tag with storage path
        $this->assertStringContainsString($prodWithImg->image_url, $content);
        // Both product names are visible
        $this->assertStringContainsString('NGK Iridium Spark Plug', $content);
        $this->assertStringContainsString('Generic Washer Fluid 1L', $content);
    }

    /**
     * 8. Verify POS index passes image_url and visible product names in JSON.
     */
    public function test_pos_includes_product_names_and_images_in_payload(): void
    {
        Storage::fake('public');

        $imgPath = UploadedFile::fake()->image('wiper.png')->store('products', 'public');

        $product = Product::create([
            'name' => 'Michelin Stealth Wiper 24"',
            'sku' => 'WPR-MCH-024',
            'uom_id' => $this->uom->id,
            'selling_price' => 5200.00,
            'image_path' => $imgPath,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('operations.direct-sales.index'));
        $response->assertOk();

        $content = $response->getContent();
        // Product name must be in the rendered payload
        $this->assertStringContainsString('Michelin Stealth Wiper', $content);
        $this->assertStringContainsString('image_url', $content);
        $this->assertStringContainsString(basename($imgPath), $content);
    }

    /**
     * 9. Verify POS searchProducts JSON endpoint returns image_url and product_name.
     */
    public function test_pos_search_endpoint_returns_image_url_and_product_name(): void
    {
        Storage::fake('public');

        $imgPath = UploadedFile::fake()->image('filter.jpg')->store('products', 'public');

        $product = Product::create([
            'name' => 'Denso Cabin Air Filter',
            'sku' => 'FLT-DEN-009',
            'uom_id' => $this->uom->id,
            'selling_price' => 2800.00,
            'image_path' => $imgPath,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('operations.direct-sales.search-products', [
            'query' => 'Denso',
        ]));

        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $product->id,
            'name' => 'Denso Cabin Air Filter',
            'product_name' => 'Denso Cabin Air Filter',
            'sku' => 'FLT-DEN-009',
            'image_url' => $product->image_url,
        ]);
    }

    /**
     * 10. Verify layout elements for persistent partial navigation and accessible sidebar.
     */
    public function test_layout_contains_main_content_and_accessible_sidebar(): void
    {
        $response = $this->actingAs($this->admin)->get(route('inventory.products.index'));
        $response->assertOk();

        $content = $response->getContent();

        // Main content area
        $this->assertStringContainsString('id="main-content"', $content);
        // Loading bar
        $this->assertStringContainsString('id="page-loading-bar"', $content);
        // Persistent sidebar
        $this->assertStringContainsString('id="app-sidebar"', $content);
        $this->assertStringContainsString('data-active-section="inventory"', $content);
        $this->assertStringContainsString('id="sidebar-nav-container"', $content);
        // Accessible attributes on parent section buttons
        $this->assertStringContainsString(':aria-expanded="activeSection === \'inventory\' ? \'true\' : \'false\'"', $content);
        $this->assertStringContainsString('aria-controls="section-inventory"', $content);
    }

    /**
     * 11. Verify direct URL access and browser refresh render successfully across submenus.
     */
    public function test_direct_url_access_and_browser_refresh_across_submenus(): void
    {
        $routes = [
            route('inventory.products.index'),
            route('inventory.grn.index'),
            route('operations.vehicles.index'),
            route('operations.job-cards.index'),
            route('operations.direct-sales.index'),
            route('billing.invoices.index'),
            route('billing.quotations.index'),
        ];

        foreach ($routes as $routeUrl) {
            $response = $this->actingAs($this->admin)->get($routeUrl);
            $response->assertOk();
            $this->assertStringContainsString('id="main-content"', $response->getContent());
        }
    }
}
