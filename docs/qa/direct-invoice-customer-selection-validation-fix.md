# VIRAN AUTO CARE — DIRECT INVOICE CUSTOMER SELECTION VALIDATION FIX REPORT

**Date:** October 28, 2026  
**System:** Viran Auto Care Management System  
**Affected Page:** `/operations/invoices/create` (Direct Invoice)  
**Testing Status:** NOT RUN — requested by user  

---

## 1. Why the Previous Fix Did Not Resolve the Issue

Although the previous attempt added vehicle-to-customer resolution logic to the controller, the error `"Please select an existing customer or vehicle, or add a new customer."` continued to trigger due to two critical issues:

1. **A Severe Logic Flaw in the Controller's `if/else` Block:**
   ```php
   // First block resolved customer:
   if (! empty($customerId)) {
       $customer = Customer::findOrFail($customerId);
   } ...

   // Second block evaluated new customer vs throw:
   if (! $customer && ! empty($validated['new_customer_name'])) {
       // Register new customer
   } else {
       // Thrown whenever $customer was already resolved!
       throw ValidationException::withMessages([
           'customer_id' => 'Please select an existing customer or vehicle, or add a new customer.',
       ]);
   }
   ```
   Because the second condition was written as `if (! $customer && ...) { ... } else { throw ... }` instead of `elseif (! $customer) { throw ... }`, whenever a registered customer **was successfully resolved**, `! $customer` evaluated to `false`, causing execution to immediately fall into the `else` branch and throw the validation exception!

2. **Attribute Stripping in the Shared Searchable Select Component:**
   In `resources/views/components/searchable-select.blade.php`, the root `<div>` did not include `{{ $attributes }}` or `{{ $attributes->merge(...) }}`.
   Because `@selected="handleEntitySelect($event.detail)"` is not declared in `@props`, Laravel Blade compiled it into the component's `$attributes` bag and **silently discarded it from the final HTML output**.
   As a result, when the user clicked a search result in the browser, the component's dispatched `'selected'` event was never caught by `invoiceForm`, preventing `handleEntitySelect()` from updating the form state.

---

## 2. Exact Root Cause Found in Code

1. **`app/Http/Controllers/Operations/InvoiceController.php` (Line ~253):**
   `else { throw ValidationException... }` unconditionally aborted invoice creation whenever `$customer` was valid. It required `elseif (! $customer) { throw ... }`.
2. **`resources/views/components/searchable-select.blade.php` (Line 61):**
   Missing `{{ $attributes->merge(['class' => 'relative w-full ' . $class]) }}` caused Blade to drop custom event listeners (`@selected`, `@cleared`).
3. **`resources/views/operations/invoices/create.blade.php` (Line 128 & Line 1041):**
   The search component container lacked `@selected` bubbling fallback, and `handleEntitySelect()` lacked prefix fallback (`v_` / `c_`).

---

## 3. Exact Files and Code Sections Changed

| File | Changes Made |
|------|--------------|
| [InvoiceController.php](file:///d:/Intern/08/autocare-system/app/Http/Controllers/Operations/InvoiceController.php#L185-L270) | 1. Added `'customer_search' => ['nullable', 'string']` to validation rules.<br>2. Fixed the control flow to `elseif (! $customer)` so resolved customers proceed to invoice creation.<br>3. Added direct lookup fallback (`Customer::find($vehicle->customer_id)`) if relation is not eager-loaded.<br>4. Enforced vehicle ownership protection (reassignment check). |
| [searchable-select.blade.php](file:///d:/Intern/08/autocare-system/resources/views/components/searchable-select.blade.php#L61-L77) | Added `{{ $attributes->merge(['class' => 'relative w-full ' . $class]) }}` on the outer `<div>` so `@selected` and `@cleared` directives are rendered to the DOM. |
| [create.blade.php](file:///d:/Intern/08/autocare-system/resources/views/operations/invoices/create.blade.php) | 1. Added `@selected="handleEntitySelect($event.detail)"` and `@cleared="clearCustomerAndVehicle()"` to the search container.<br>2. Enhanced `handleEntitySelect()` to parse `item.id` (`v_...` / `c_...`) and sync DOM inputs.<br>3. Initialized `$selectedVehicleId` from old/query inputs. |

---

## 4. Actual Search Result Data Structure

From `app/Http/Controllers/SearchController.php::customersAndVehicles`:

### Vehicle Search Hit:
```json
{
  "id": "v_5",
  "customer_id": 12,
  "vehicle_id": 5,
  "customer_name": "Dinesh Perera",
  "customer_phone": "0771234567",
  "customer_email": "dinesh@example.com",
  "vehicle_number": "CP HIL-2020",
  "vehicle_type": "SUV / Double Cab",
  "vehicle_type_id": 3,
  "label": "CP HIL-2020 — Dinesh Perera",
  "subtext": "Vehicle: CP HIL-2020 (SUV / Double Cab) • Owner: Dinesh Perera • 0771234567"
}
```

### Customer Search Hit (Without Vehicle):
```json
{
  "id": "c_12",
  "customer_id": 12,
  "vehicle_id": null,
  "customer_name": "Dinesh Perera",
  "customer_phone": "0771234567",
  "customer_email": "dinesh@example.com",
  "vehicle_number": null,
  "vehicle_type": "",
  "vehicle_type_id": null,
  "label": "Dinesh Perera (Customer)",
  "subtext": "Phone: 0771234567 • dinesh@example.com"
}
```

---

## 5. Correct Field Binding and Submission Handling

1. **Event Capture & State Setting:**
   - With `searchable-select.blade.php` rendering `$attributes`, `@selected="handleEntitySelect($event.detail)"` fires upon item selection.
   - `handleEntitySelect()` extracts `item.customer_id`, `item.vehicle_id`, and falls back to prefix parsing (`v_` / `c_`) or `this.vehicles` collection lookup.
2. **Hidden Inputs Synced:**
   - `<input type="hidden" name="customer_id" id="hidden_customer_id" :value="selectedCustomerId" x-model="selectedCustomerId">`
   - `<input type="hidden" name="vehicle_id" id="hidden_vehicle_id" :value="selectedVehicleId" x-model="selectedVehicleId">`
   - Both `.value` property and attribute are updated simultaneously.
3. **Summary Badge Displayed:**
   - `selectedCustomerName` becomes truthy, unhiding the summary card displaying Customer Name, Vehicle Number, and "Registered" badge.

---

## 6. Controller Validation and Ownership Handling

In `InvoiceController::store()`:
```php
// 1. Resolve Customer
if (! empty($customerId)) {
    $customer = Customer::findOrFail($customerId);
} elseif ($vehicle && $vehicle->customer) {
    $customer = $vehicle->customer;
    $customerId = $customer->id;
} elseif ($vehicle && $vehicle->customer_id) {
    $customer = Customer::find($vehicle->customer_id);
    if ($customer) {
        $customerId = $customer->id;
    }
}

// 2. Handle New Customer or Throw if No Customer Found
if (! $customer && ! empty($validated['new_customer_name'])) {
    // Auto-registration logic
} elseif (! $customer) {
    throw ValidationException::withMessages([
        'customer_id' => 'Please select an existing customer or vehicle, or add a new customer.',
    ]);
}

// 3. Enforce Ownership
if ($vehicle && $vehicle->customer_id && $customer && $vehicle->customer_id !== $customer->id) {
    $otherOwner = $vehicle->customer?->customer_name ?? 'another customer';
    throw ValidationException::withMessages([
        'vehicle_id' => "This vehicle is registered to {$otherOwner}. It cannot be assigned to {$customer->customer_name}.",
    ]);
}
```

---

## 7. How Registered and New Customers Are Handled

- **Existing Customer (No Vehicle):** `customer_id` is validated and loaded; `vehicle_id` is null; invoice is created without vehicle snapshot.
- **Existing Vehicle + Registered Customer:** `vehicle_id` and `customer_id` are loaded; ownership is verified; invoice is created with both customer and vehicle snapshot.
- **New Customer (Popup Workflow):** `new_customer_name` and `new_customer_mobile` are validated; deduplicated by phone; customer and optional vehicle created in database transaction.

---

## 8. Confirmation of Scope Integrity

The following areas were NOT modified:
- Quotations, Job Cards, Direct Sales, Payroll, Inventory, GRN, Expenses, Dashboard, Sidebar, Global CSS
- Invoice service items, products, custom lines, pricing, discounts, tax, and totals
- Invoice numbering and payment processing

---

## 9. Testing Status

**NOT RUN — requested by user.**

In strict adherence to instructions:
- No browser or Playwright automation was executed.
- No Laravel server was started.
- No test suites (PHPUnit, Pest) were run.
- No database queries, migrations, or seeders were executed.
- No test invoices were created.
- Formatters (Pint) were NOT run per explicit instruction.
