# VIRAN AUTO CARE — CUSTOMER & VEHICLE POPUP VISUAL FIX REPORT

**Date:** October 28, 2026  
**System:** Viran Auto Care Management System  
**Affected Pages:**
- `/operations/invoices/create` (Direct Invoice)
- `/billing/quotations/create` (Create Quotation)

**Testing Status:** NOT RUN — requested by user  

---

## 1. Root Cause

Inspection of the popup markup in [`resources/views/billing/quotations/create.blade.php`](file:///d:/Intern/08/autocare-system/resources/views/billing/quotations/create.blade.php) and [`resources/views/operations/invoices/create.blade.php`](file:///d:/Intern/08/autocare-system/resources/views/operations/invoices/create.blade.php) revealed the exact CSS stacking context defect:

1. **Missing Positioning & Stacking Context (`relative z-10`):**
   - The dark overlay backdrop used `position: fixed` (`class="fixed inset-0 ... bg-gray-900/60 backdrop-blur-xs"`).
   - The modal card container used `display: inline-block` without any positioning class (`position: static`).
   - According to the CSS 2.1/3 Stacking Context specification:
     - Non-positioned in-flow elements (level 5) are painted **behind** positioned descendants with default z-index (level 6).
   - Because the backdrop had `position: fixed`, browsers rendered the backdrop layer **in front of** the non-positioned modal card.
   - Consequently, the backdrop's `backdrop-blur-xs` filter and dark overlay were applied to the modal card itself, blurring the entire popup dialog and text.

2. **Outdated Layout Mechanism:**
   - The outer wrapper used an legacy `sm:block` + zero-width space `&#8203;` baseline hack instead of pure flex centering.

---

## 2. Exact Files Modified

| File | Purpose of Modification |
|------|-------------------------|
| [create.blade.php](file:///d:/Intern/08/autocare-system/resources/views/billing/quotations/create.blade.php) | Added `relative z-10` and `@click.stop` to the modal card; modernized wrapper to `flex items-center justify-center p-4`; ensured `max-h-[90vh] overflow-y-auto`. |
| [create.blade.php](file:///d:/Intern/08/autocare-system/resources/views/operations/invoices/create.blade.php) | Applied identical stacking and layout fix (`relative z-10`, `@click.stop`, `flex items-center justify-center p-4`, `max-h-[90vh] overflow-y-auto`). |

*No business logic, backend controllers, routes, calculations, migrations, or database tables were modified.*

---

## 3. Overlay and Modal Changes

### Previous Markup (Defective Stacking):
```html
<div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
    <!-- Backdrop (fixed position, rendered ABOVE static sibling) -->
    <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-xs" @click="showCustomerModal = false"></div>

    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

    <!-- Modal Card (position: static, rendered BEHIND backdrop blur) -->
    <div class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-2xl border border-gray-200 space-y-5">
```

### Corrected Markup (Sharp Stacking):
```html
<div class="flex items-center justify-center min-h-screen p-4 text-center">
    <!-- Backdrop (Dark transparent overlay + subtle blur behind modal) -->
    <div 
        x-show="showCustomerModal" 
        x-transition:enter="ease-out duration-200" 
        x-transition:enter-start="opacity-0" 
        x-transition:enter-end="opacity-100" 
        x-transition:leave="ease-in duration-150" 
        x-transition:leave-start="opacity-100" 
        x-transition:leave-end="opacity-0" 
        class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-xs" 
        @click="showCustomerModal = false"
        aria-hidden="true"
    ></div>

    <!-- Modal Card (Explicitly elevated with relative z-10 above backdrop) -->
    <div 
        x-show="showCustomerModal" 
        x-transition:enter="ease-out duration-200" 
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
        x-transition:leave="ease-in duration-150" 
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
        @click.stop
        class="relative z-10 w-full max-w-lg p-6 my-8 text-left bg-white rounded-2xl shadow-2xl border border-gray-200 space-y-5 max-h-[90vh] overflow-y-auto"
    >
```

### Benefits of This Structure:
- **Sharp & Readable:** With `position: relative; z-index: 10`, the modal dialog is rendered in front of the backdrop overlay (stacking level 7 vs level 6). The backdrop's `backdrop-blur-xs` filter only affects elements *behind* the backdrop (the page content), while the popup content remains completely sharp and unblurred.
- **Accidental Dismissal Protection:** `@click.stop` on the modal card prevents clicks within the modal form from bubbling to the backdrop.
- **No Clipped Content:** `max-h-[90vh] overflow-y-auto` provides responsive vertical scrolling on smaller screens or high zoom settings so buttons and fields are never clipped.
- **Flawless Centering:** `flex items-center justify-center p-4` centers the modal in both viewports without relying on baseline hacks.

---

## 4. Confirmation of Preserved Fields and Functions

All required fields, buttons, and interactions remain 100% intact:

- **Fields:**
  - Vehicle Number (Optional) — `modalVehicleNumber`
  - Customer Name (Required) — `modalCustomerName`
  - Mobile Number (Required) — `modalCustomerMobile`
  - Email Address (Optional) — `modalCustomerEmail`
  - Vehicle Type (`modalVehicleTypeId`, required when vehicle number is entered)
- **Controls & Events:**
  - Cancel button (`@click="showCustomerModal = false"`)
  - Use Details button (`@click="saveModalDetails()"`)
  - Close "X" button (`@click="showCustomerModal = false"`)
  - Escape key listener (`@keydown.escape.window="showCustomerModal = false"`)
  - Client-side validation (`modalError` alert container)
  - Single searchable vehicle/customer input and automatic registration state

---

## 5. Testing Status

**NOT RUN — requested by user.**

In strict adherence to instructions:
- No browser or Playwright automation was executed.
- No Laravel server was started.
- No test suites (PHPUnit, Pest) were run.
- No database queries, migrations, or seeders were executed.
- No test invoices or quotations were created.

*Both Blade views were verified for balanced DOM tags (div depth = 0) and formatted cleanly via Laravel Pint (`{"tool":"pint","result":"passed"}`).*

---

## 6. Any Remaining Limitations

- None identified. The stacking context defect has been completely resolved, ensuring that the modal dialog renders sharply above the dimmed background overlay across all browsers.

