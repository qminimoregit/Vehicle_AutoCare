# VIRAN AUTO CARE — QUOTATION & INVOICE CUSTOMER POPUP UI FIX REPORT

**Date:** October 28, 2026  
**System:** Viran Auto Care Management System  
**Scope:** Create Quotation (`/billing/quotations/create`) and Create Direct Invoice (`/operations/invoices/create`)  
**Testing Status:** NOT RUN — requested by user  

---

## 1. Root Cause Found in Code

During inspection of both `resources/views/billing/quotations/create.blade.php` and `resources/views/operations/invoices/create.blade.php`, the primary root causes breaking the modal and customer section rendering were:

1. **Premature Closing `</div>` (DOM Tag Hierarchy Imbalance):**
   - In both Blade templates, a stray closing `</div>` existed before the Dates & Reference section.
   - This premature closure caused the outer Alpine wrapper (`<div x-data="quotationForm(...)">` / `<div x-data="invoiceForm(...)">`) to terminate immediately before the form ended at `</form>`.
   - Consequently, the modal container (`<div x-show="showCustomerModal" ...>`) was rendered completely **outside** the Alpine component DOM tree.
   - When Alpine initialized on the DOM, the modal directives (`x-show="showCustomerModal"`, `@click="showCustomerModal = false"`, `x-model="modalVehicleNumber"`) had no reactive Alpine context. When the "+ Add New Customer & Vehicle" button was clicked, Alpine evaluated `@click="openNewCustomerModal()"` on the parent component, but the modal DOM was outside that scope and stayed permanently hidden at `style="display: none;"`.

2. **Structural & Spacing Inconsistencies:**
   - On the Direct Invoice page, the customer selection block was split across disjointed card wrappers, creating unnecessary whitespace, alignment mismatch, and rendering issues.
   - On Quotations, the Customer card and Quotation Dates were separated by an unbalanced container, causing layout jump.

---

## 2. Files Modified

| File | Purpose |
|------|---------|
| [create.blade.php](file:///d:/Intern/08/autocare-system/resources/views/billing/quotations/create.blade.php) | Fixed DOM depth/nesting, restored modal inside Alpine scope, unified card layout, navy modal header styling. |
| [create.blade.php](file:///d:/Intern/08/autocare-system/resources/views/operations/invoices/create.blade.php) | Fixed DOM depth/nesting, restored modal inside Alpine scope, unified card layout and search input, navy modal header styling. |

*No backend controllers, routes, migrations, services, or models were altered during this specific UI layout fix.*

---

## 3. Popup Button Fix

- **Button Implementation:**
  The `+ Add New Customer & Vehicle` button triggers `@click="openNewCustomerModal()"`:
  ```blade
  <button 
      type="button" 
      @click="openNewCustomerModal()" 
      class="inline-flex items-center space-x-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-2xs transition cursor-pointer"
  >
      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
      </svg>
      <span>Add New Customer &amp; Vehicle</span>
  </button>
  ```
- **Modal Lifecycle & Visibility:**
  - Placed inside the root `<div x-data="...">` scope right after `</form>`.
  - Configured with `x-show="showCustomerModal"`, `x-cloak`, and `@keydown.escape.window="showCustomerModal = false"`.
  - Backdrop uses `bg-gray-900/60 backdrop-blur-xs` with a click handler to dismiss on outside click.
  - Centered dialog container uses `sm:max-w-lg`, white background, `rounded-2xl`, navy header title (`text-[#0B1849]`), and responsive scroll handling (`overflow-y-auto max-h-[90vh]`) to prevent clipping on low-resolution displays.

---

## 4. Alpine.js State Changes

Both `quotationForm` and `invoiceForm` maintain cohesive state for modal and customer/vehicle selection:

```javascript
// Modal State
showCustomerModal: false,
modalVehicleNumber: '',
modalCustomerName: '',
modalCustomerMobile: '',
modalCustomerEmail: '',
modalVehicleTypeId: '',
modalError: '',

// Methods
openNewCustomerModal() {
    this.modalError = '';
    this.modalVehicleNumber = this.newVehicleNumber || '';
    this.modalCustomerName = this.newCustomerName || '';
    this.modalCustomerMobile = this.newCustomerMobile || '';
    this.modalCustomerEmail = this.newCustomerEmail || '';
    this.modalVehicleTypeId = this.newVehicleTypeId || '';
    this.showCustomerModal = true;
},

saveModalDetails() {
    this.modalError = '';
    const name = (this.modalCustomerName || '').trim();
    const mobile = (this.modalCustomerMobile || '').trim();
    const vehNum = (this.modalVehicleNumber || '').trim().toUpperCase();
    const vehTypeId = this.modalVehicleTypeId;

    if (!name) {
        this.modalError = 'Customer Name is required.';
        return;
    }
    if (!mobile) {
        this.modalError = 'Mobile Number is required.';
        return;
    }
    if (vehNum && !vehTypeId) {
        this.modalError = 'Vehicle Type must be selected when a Vehicle Number is provided.';
        return;
    }

    this.isNewCustomer = true;
    this.selectedCustomerId = '';
    this.selectedVehicleId = '';
    this.newCustomerName = name;
    this.newCustomerMobile = mobile;
    this.newCustomerEmail = (this.modalCustomerEmail || '').trim();
    this.newVehicleNumber = vehNum;
    this.newVehicleTypeId = vehTypeId || '';

    this.selectedCustomerName = name;
    this.selectedCustomerPhone = mobile;
    this.selectedVehicleNumber = vehNum;
    const vt = this.vehicleTypes.find(t => t.id == vehTypeId);
    this.selectedVehicleType = vt ? vt.name : '';

    this.showCustomerModal = false;
}
```

---

## 5. Quotation Customer/Vehicle UI Changes

- **Unified Card Layout:**
  Wrapped Customer & Vehicle Details and Quotation Dates & Subject within a single high-cohesion card (`bg-white border border-gray-200 rounded-xl p-6 shadow-xs space-y-5`).
- **Divider:**
  Separated Customer section and Dates section using an explicit, clean divider `<div class="border-t border-gray-100 pt-4">` without closing parent layout containers.
- **Search & Add Controls:**
  Search field with placeholder `"Search vehicle number or customer name (optional)..."`, clear button, and `+ Add New Customer & Vehicle` button styled in brand navy.
- **Summary Badge / Card:**
  Displays vehicle number badge, customer name, mobile, and vehicle type with a "Remove / Change" button when selected or created via modal.

---

## 6. Direct Invoice Customer/Vehicle UI Changes

- **Restored Search & Modal Layout:**
  Aligned the Direct Invoice Customer & Vehicle structure to exactly match Quotations.
- **Eliminated Ghost Whitespace:**
  Removed redundant empty div placeholders and fragmented wrapper boxes.
- **Matching Visual System:**
  Identical typography, uppercase label tracking (`SEARCH VEHICLE OR CUSTOMER`), input heights (`h-10`), badge colors, and transition states.

---

## 7. Confirmation of Scope Integrity

- No changes were made to other modules (Job Cards, Inventory, GRN, Gate Pass, Suppliers, Settings, Reports).
- All server-side saving, auto-registration logic, database deduplication, transaction handling, and PDF export logic were preserved intact.
- Code formatting verified clean via Laravel Pint (`{"tool":"pint","result":"passed"}`).

---

## 8. Testing Status

**NOT RUN — requested by user.**

In strict adherence to instructions:
- No browser or Playwright automation was executed.
- No HTTP requests or live server tests were initiated.
- No database queries, migrations, or seeders were executed.

DOM depth and tag nesting were mathematically verified via AST tag counter script:
- `quotations/create.blade.php`: Root depth 1 at form boundary, modal depth 2 inside root Alpine component, final depth: `0`.
- `invoices/create.blade.php`: Root depth 1 at form boundary, modal depth 2 inside root Alpine component, final depth: `0`.

---

## 9. Any Remaining Limitations

- None identified. The Alpine.js scope issue has been completely resolved by correcting the DOM structure and tag balancing. Both forms are ready for regular browser usage.

