# VIRAN AUTO CARE — SEARCHABLE ITEM FIELDS & OPTIONAL VEHICLE DETAILS CHANGE REPORT

**Document Path:** `docs/qa/searchable-grn-quotation-invoice-fields.md`  
**Application:** Viran Auto Care Management System  
**Framework:** Laravel 13, Blade, Tailwind CSS, Alpine.js  
**Execution Mode:** Static Code Implementation & Verification (Zero Testing / Zero Automation Executed)  

---

## 1. Existing Implementation Inspected

Before making changes, an end-to-end architecture and code inspection was conducted across the target modules:

1. **Purchasing (GRN) (`/inventory/grn/create`):**
   - **Controller:** `GoodsReceivedNoteController::create()` loaded all active products (`Product::active()->with('uom')->get(...)`) into view variable `$products`.
   - **View:** `resources/views/inventory/grn/create.blade.php` used an Alpine `x-for` loop iterating over an `items` array. The Product column rendered a standard HTML `<select :name="items[${index}][product_id]">` populated with `<option>` tags. With extensive inventory catalogs, this static dropdown became unwieldy and lacked real-time filtering, SKU searching, and keyboard navigation.
   - **Calculations:** UOM display, purchase unit price, purchase quantity, free promotional units, line totals, and grand total calculations relied on `item.product_id`, `item.uom_name`, and `item.selling_price_snapshot`.

2. **Quotations (`/billing/quotations/create`):**
   - **Controller:** `QuotationController::create()` fetched active customers, vehicles, services, and products. `QuotationController::store()` strictly validated `customer_id` (`required|exists:customers,id`) and optional `vehicle_id` (`nullable|exists:vehicles,id`).
   - **View:** `resources/views/billing/quotations/create.blade.php` rendered `<select>` elements for service line items and product line items. Buttons contained duplicate plus markers (e.g., text `+ Add Service` beside icons). Vehicle selection was limited to a rigid registered vehicle `<select>` tied exclusively to registered customers.

3. **Direct Invoices (`/operations/invoices/create`):**
   - **Controller:** `InvoiceController::create()` loaded products, services, customers, and vehicles. `InvoiceController::store()` required `customer_id` and had no direct vehicle capture for manual over-the-counter invoices.
   - **View:** `resources/views/operations/invoices/create.blade.php` similarly used long `<select>` dropdowns for line item selection.

4. **Database Schema & Constraints:**
   - `quotations` table: Foreign key `customer_id` (NOT NULL, cascade on delete), `vehicle_id` (nullable, null on delete), `customer_snapshot` (json nullable), `vehicle_snapshot` (json nullable).
   - `invoices` table: Foreign key `customer_id` (NOT NULL, cascade on delete), `customer_snapshot` (json nullable). No `vehicle_id` column currently on `invoices` table.
   - `customers` table: Existing default system record (`id = 43`, phone `0000000000`, `customer_name = 'Walk-in Customer'`) provides safe foreign-key fallback for ad-hoc manual customers without generating duplicate records or inventing non-existent database keys.

---

## 2. Files Modified

| File Path | Description of Changes |
|---|---|
| `resources/views/inventory/grn/create.blade.php` | Replaced standard product `<select>` in dynamic GRN rows with an Alpine searchable combobox (`"Search product name, SKU or part number..."`), live client-side filtering, keyboard navigation, clear button, and retained all quantity and pricing calculations. |
| `resources/views/billing/quotations/create.blade.php` | Replaced service/product `<select>` elements with searchable comboboxes. Added customer mode toggle (Registered Customer vs. Manual Customer). Added vehicle plate search and manual vehicle entry (plate, type, make/model). Normalized action buttons to have exactly one plus icon. |
| `app/Http/Controllers/Billing/QuotationController.php` | Passed `$vehicleTypes` to `create()`. Updated `store()` validation and resolution to support optional manual customer details and registered/unregistered vehicle snapshots without schema violation. |
| `resources/views/billing/quotations/show.blade.php` | Updated customer and vehicle display cards to check snapshots for manual customer details and unregistered vehicle details before falling back to relational models. |
| `resources/views/billing/quotations/pdf.blade.php` | Updated printable PDF template to render customer name, telephone, vehicle registration number, and make/model from snapshot data when manual or unregistered. |
| `resources/views/operations/invoices/create.blade.php` | Added searchable service/product comboboxes to dynamic lines. Added Customer Mode toggle (Registered vs. Manual). Added vehicle plate search and manual vehicle entry section. Standardized button icons to single plus SVGs. |
| `app/Http/Controllers/Operations/InvoiceController.php` | Passed `$vehicleTypes` and preselected customer to `create()`. Updated `store()` to support manual customer details and store vehicle details in `customer_snapshot`. |
| `resources/views/operations/invoices/show.blade.php` | Updated Billed To and Service Details cards to display customer snapshot information and vehicle snapshot details when direct invoice has no linked workshop Job Card. |
| `resources/views/operations/invoices/pdf.blade.php` | Updated printable invoice PDF template to render manual customer name/phone and direct vehicle plate/type/make-model from snapshot data. |
| `app/Http/Controllers/SearchController.php` | Enhanced `/search/vehicles` endpoint to include `make_model`, `vehicle_type`, `vehicle_type_id`, `owner_name`, and `owner_phone` for seamless front-end auto-population. |
| `database/migrations/2026_10_28_120000_make_customer_and_vehicle_optional_on_billing_tables.php` | Minimal additive migration prepared for future schema migration to make `customer_id` nullable and add direct vehicle columns to `invoices`. **(LEFT UNRUN AS INSTRUCTED)** |

---

## 3. Searchable GRN Product Selection

**Page:** `/inventory/grn/create`

- **Input Control:** Replaced `<select :name="items[${index}][product_id]">` with an inline Alpine combobox component scoped per dynamic row.
- **Placeholder:** `"Search product name, SKU or part number..."`
- **Filtering Logic:**
  - Case-insensitive search matching `p.name`, `p.sku`, and `p.part_number` (or description).
  - Maximum 25 matching results displayed to preserve rapid DOM rendering and responsiveness.
- **Keyboard Navigation:**
  - `ArrowDown`: Navigates to next highlighted item.
  - `ArrowUp`: Navigates to previous highlighted item.
  - `Enter`: Selects the currently highlighted product.
  - `Escape`: Closes the search dropdown panel.
- **Selection State & Display:**
  - Displays product name in bold text alongside a monospace `SKU: [code]` tag and unit of measurement badge.
  - Shows clear button (`X` icon) when a search query or product is present.
  - Correct product database ID is submitted via hidden input `<input type="hidden" :name="items[${index}][product_id]" :value="item.product_id" required>`.
- **Preserved Calculations:**
  - Selection automatically triggers `item.uom_name` and `item.selling_price_snapshot` updates.
  - Existing purchase price, purchased quantity, free quantity, line totals (`lineTotal(item)`), and supplier grand total (`grandTotal()`) calculations remain 100% intact.
  - Dynamic rows added via `addItem()` instantly inherit the identical searchable selector.

---

## 4. Searchable Quotation Items

**Page:** `/billing/quotations/create`

- **Line Item Selectors:**
  - **Service Lines:** Searchable combobox filtering master services by Name and Service Code (`s.name`, `s.code`).
  - **Product Lines:** Searchable combobox filtering inventory catalog by Product Name, SKU, and Part Number (`p.name`, `p.sku`, `p.part_number`). Shows selling price and available warehouse stock.
  - **Custom Lines:** Displays editable item name and specification text fields without pre-defined lookup restrictions.
- **Data Population Upon Selection:**
  - Updates `item.item_id`, `item.item_code`, `item.item_name`, and `item.description`.
  - Sets unit price to applicable service price (`s.default_price`) or product selling price (`p.selling_price`).
  - Automatically updates line item unit (`Job` for services, `Unit`/`Pcs` for spare parts).
  - Maintains line quantity and line discount controls.
  - Automatically recalculates line totals, document-level discounts, tax amounts, additional charges, and document grand total.
- **Button Normalization:**
  - Header actions: `Add Service`, `Add Product`, and `Custom Item`.
  - Each button features **exactly one visible plus icon** (clean SVG vector), eliminating duplicate `+` text markers.

---

## 5. Searchable Invoice Items

**Page:** `/operations/invoices/create`

- **Line Item Selectors:**
  - Integrated the identical searchable combobox components developed for Quotations.
  - Search services by name and code; search products by name, SKU, and part number with real-time stock indicators.
- **Behavior:**
  - Selecting an item populates `item_id`, `item_code`, `item_name`, `description`, `unit`, and `unit_price`.
  - Full keyboard accessibility (`ArrowUp`, `ArrowDown`, `Enter`, `Escape`) and clear action (`X`).
  - Automatically triggers `calculateLineTotal(item)`, `calculateSubtotal()`, `calculateTaxAmount()`, and `calculateGrandTotal()`.
- **Button Icons:**
  - Actions: `Add Service`, `Add Product`, and `Custom Line`.
  - Verified and styled to display **exactly one visible plus icon** per button.

---

## 6. Customer Detail Handling

**Affected Pages:** `/billing/quotations/create` & `/operations/invoices/create`

- **Dual-Mode Customer Toggle:**
  - Clearly styled pill switcher: `[Registered Customer]` vs. `[Manual / Unregistered]`.
- **Registered Mode:**
  - Utilizes the existing fast `/search/customers` endpoint via `x-searchable-select`.
  - Selecting a customer links `customer_id` and filters vehicle lookup to vehicles owned by that customer.
- **Manual Mode:**
  - Provides direct text inputs for:
    1. **Customer Name** (`manual_customer_name` — required in manual mode).
    2. **Telephone Number** (`manual_customer_phone` — optional phone format).
  - Displays an informative visual badge: `"Manual customer details are saved to this document snapshot without permanent customer registration."`
- **Database Safety & Deduplication:**
  - In controller `store()` methods, if manual mode is used:
    1. System first checks if a registered customer exists with that telephone number. If matched, links to that customer ID to prevent duplicate records.
    2. If no phone match exists, links to the existing default system Walk-in customer (`id = 43`, phone `0000000000`).
    3. The actual manually typed name and telephone are preserved in `customer_snapshot['name']` and `customer_snapshot['phone']` (and invoice `customer_snapshot['customer_name']`).
    4. Satisfies foreign key constraints (`customer_id NOT NULL`) without database corruption or inventing fake IDs.

---

## 7. Registered Vehicle Behaviour

**Affected Pages:** `/billing/quotations/create` & `/operations/invoices/create`

- **Search & Selection:**
  - When typing in the vehicle field, existing registered vehicles are searched in real-time by registration plate and make/model.
  - Displays registration plate, make/model, vehicle type, and owner name/phone.
- **Ownership Verification:**
  - If a customer is selected, registered vehicle results are filtered strictly to vehicles belonging to that customer.
  - Prevents vehicle mismatching against another customer's profile.
- **Auto-Population:**
  - Selecting a registered vehicle automatically links `vehicle_id`.
  - Auto-populates vehicle registration number, vehicle type, and make/model.
  - If no customer was selected yet, selecting a registered vehicle automatically sets and selects the vehicle's registered owner.

---

## 8. Unregistered Vehicle Handling

**Affected Pages:** `/billing/quotations/create` & `/operations/invoices/create`

- **Ad-Hoc Vehicle Entry:**
  - Vehicle registration is **strictly optional**. For general sales or non-vehicle quotes, vehicle fields can be left completely blank.
  - When typing a new or unregistered plate (e.g. `WP-CAB-9988`), a selectable dropdown option appears:  
    `"Use '[PLATE]' as manual / unregistered vehicle"`
- **Manual Fields Displayed:**
  - When manual vehicle mode is active or an unregistered plate is entered:
    1. **Vehicle Registration Number:** Stored from typed plate in uppercase format.
    2. **Vehicle Type:** Dropdown populated from active system `VehicleType` records (Car, SUV, Van, Motorcycle, Truck, etc.).
    3. **Make / Model:** Optional free-text field (e.g., `Toyota Hilux 2020`).
- **Snapshot Storage:**
  - Unregistered vehicle details are saved directly into `vehicle_snapshot` (for Quotations) and `customer_snapshot` / `subject` (for Invoices).
  - `vehicle_id` is stored as `null`.
  - Does **not** insert records into `vehicles` table.
  - Does **not** redirect or force the user to Vehicle Registration.

---

## 9. Database Changes Required, If Any

1. **Current Production Schema:**
   - Works safely with existing schema:
     - Foreign key `customer_id` is satisfied via existing Walk-in customer anchor when manual customer details are supplied.
     - Quotations table already contains nullable `vehicle_id`, `customer_snapshot` (JSON), and `vehicle_snapshot` (JSON).
     - Invoices table contains nullable `customer_snapshot` (JSON) where manual customer and vehicle attributes are safely stored.
2. **Prepared Additive Migration (UNRUN):**
   - **Migration File:** `database/migrations/2026_10_28_120000_make_customer_and_vehicle_optional_on_billing_tables.php`
   - **Scope:**
     - Modifies `quotations.customer_id` to `nullable()`.
     - Modifies `invoices.customer_id` to `nullable()`.
     - Adds `invoices.vehicle_id` (foreignId constrained `vehicles`, nullable, null on delete).
     - Adds `invoices.vehicle_snapshot` (JSON nullable).
   - **Status:** **NOT RUN**. File is committed for future scheduled maintenance windows without executing `php artisan migrate`.

---

## 10. Unchanged Modules

Strict boundary controls were adhered to throughout implementation:

- **Unchanged Core UI:** Header, sidebar navigation, top bar, breadcrumbs, and overall dashboard layout remain identical.
- **Unchanged Operations:** Job Cards, Workshop Technician Assignments, Job Card Inspections, and Vehicle Delivery workflows were not altered.
- **Unchanged POS Module:** Direct Sales (POS) cash register, product scanning, and pricing modals remain untouched.
- **Unchanged Financials:** Payments, payment receipts, multiple invoice split payments, invoice numbering series, and ledger postings remain intact.
- **Unchanged Purchasing:** GRN confirmation, inventory batches, FIFO stock movements, and valuation logic were not changed.
- **Unchanged Styling:** Tailwind color palettes (`#0B1849`), typography, and container padding remain consistent with design standards.

---

## 11. Testing Status

| Verification Activity | Status | Notes |
|---|---|---|
| Open Google Chrome / CDP | **NOT RUN** | As explicitly requested in prompt instructions. |
| Playwright Automation Scripts | **NOT RUN** | As explicitly requested in prompt instructions. |
| Laravel Server (`php artisan serve`) | **NOT RUN** | As explicitly requested in prompt instructions. |
| PHPUnit / Pest Automated Tests | **NOT RUN** | As explicitly requested in prompt instructions. |
| NPM Build / Dev Watchers | **NOT RUN** | As explicitly requested in prompt instructions. |
| Database Queries / Direct SQL | **NOT RUN** | As explicitly requested in prompt instructions. |
| Database Migrations (`php artisan migrate`) | **NOT RUN** | As explicitly requested in prompt instructions. |
| Code Formatting (`vendor/bin/pint --dirty`) | **EXECUTED** | Formatted modified PHP classes to project coding standards. |

---
**Report Completed By:** Senior Laravel & Alpine.js Developer  
**Date:** October 28, 2026

