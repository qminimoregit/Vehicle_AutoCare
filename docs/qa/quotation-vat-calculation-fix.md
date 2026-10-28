# QA Report: Quotation Tax / VAT (%) Calculation & Reactivity Fix

**Project:** Viran Auto Care Management System (`D:\Intern\08\autocare-system`)  
**Affected Page:** `http://localhost:8000/billing/quotations/create`  
**Date:** 2026-10-28  
**QA Status:** Completed (Code & Logic Verification)  
**Test Runner Execution:** **NOT RUN — requested by user**

---

## 1. Executive Summary

During quotation creation in the Viran Auto Care system, the Calculation Summary card displayed a static `0` for Tax / VAT (%) and `+ 0.00` for the calculated VAT amount. The objective of this intervention was to inspect, verify, and fix the reactive calculation flow of the Tax / VAT field on the Create Quotation page while preserving strict formula fidelity, decimal precision, edge-case safety, and backend persistence consistency.

The fix was applied exclusively to the quotation creation component (`resources/views/billing/quotations/create.blade.php`). The backend calculations in `QuotationController` were verified to ensure authoritative, server-side VAT and Grand Total re-computation. No visual redesign was introduced, no test runners were executed, and no unrelated modules were modified.

---

## 2. Root Cause Analysis

Investigation of `resources/views/billing/quotations/create.blade.php` and its Alpine.js component (`quotationForm`) revealed the following issues:

1. **Missing Initial State Rehydration in Alpine Config:**
   - In `create.blade.php`, the Alpine initialization `x-data="quotationForm({ ... })"` omitted `taxPercentage`, `discountType`, `discountValue`, and `additionalCharges`.
   - The internal Alpine model hardcoded `taxPercentage: 0`.
   - Consequently, if a form failed validation on another field or was rehydrated with previous input (`old('tax_percentage')`), the tax percentage and summary values were wiped back to 0.

2. **Narrow Amount Display Width (`w-16`):**
   - The VAT amount readout span was constrained to `w-16` (64px).
   - Formatted VAT amounts exceeding 4 digits (e.g., `+ 1,800.00` or `+ 18,000.00`) suffered from truncation or awkward line-wrapping risks in narrow layouts.

3. **Input Sanitization & NaN Defense:**
   - When a user cleared the numeric input in the browser, `x-model.number` could assign an empty string or `null`. While `parseFloat(...) || 0` provided basic fallback, explicit guardrails preventing negative inputs or values exceeding 100% were needed to guarantee mathematically sound, NaN-free computations across `calculateTaxAmount()` and `calculateGrandTotal()`.

---

## 3. Code Changes

### Files Modified:
- `resources/views/billing/quotations/create.blade.php`

### Detailed Diff Summary:

```diff
--- a/resources/views/billing/quotations/create.blade.php
+++ b/resources/views/billing/quotations/create.blade.php
@@ -15,7 +15,11 @@
         oldItems: {{ Js::from(old('items', [])) }},
+        taxPercentage: {{ (float) old('tax_percentage', 0) }},
+        discountType: '{{ old('discount_type', 'none') }}',
+        discountValue: {{ (float) old('discount_value', 0) }},
+        additionalCharges: {{ (float) old('additional_charges', 0) }}
     })"
 
@@ -671,8 +675,8 @@
                             name="tax_percentage" 
                             x-model.number="taxPercentage"
                             placeholder="0.00"
-                            class="w-20 px-2 py-1 text-xs text-right border border-gray-300 rounded-lg"
+                            class="w-20 px-2 py-1 text-xs text-right border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0B1849] focus:border-[#0B1849]"
                         />
-                        <span class="text-xs text-gray-400 w-16 text-right" x-text="'+ ' + formatMoney(calculateTaxAmount())"></span>
+                        <span class="text-xs text-gray-400 min-w-[5rem] text-right" x-text="'+ ' + formatMoney(calculateTaxAmount())"></span>
                     </div>
                 </div>
 
@@ -904,10 +908,10 @@ function quotationForm(config) {
-        discountType: 'none',
-        discountValue: 0,
-        taxPercentage: 0,
-        additionalCharges: 0,
+        discountType: config.discountType || 'none',
+        discountValue: parseFloat(config.discountValue) || 0,
+        taxPercentage: parseFloat(config.taxPercentage) || 0,
+        additionalCharges: parseFloat(config.additionalCharges) || 0,
         items: [],
 
@@ -1077,49 +1081,58 @@ function quotationForm(config) {
         calculateLineTotal(item) {
-            const qty = parseFloat(item.quantity) || 0;
-            const price = parseFloat(item.unit_price) || 0;
+            const qty = Math.max(0, parseFloat(item.quantity) || 0);
+            const price = Math.max(0, parseFloat(item.unit_price) || 0);
             const lineSubtotal = qty * price;
 
             let discount = 0;
+            const discVal = Math.max(0, parseFloat(item.discount_value) || 0);
             if (item.discount_type === 'percentage') {
-                discount = lineSubtotal * ((parseFloat(item.discount_value) || 0) / 100);
+                const pct = Math.min(100, discVal);
+                discount = lineSubtotal * (pct / 100);
             } else if (item.discount_type === 'fixed') {
-                discount = parseFloat(item.discount_value) || 0;
+                discount = Math.min(lineSubtotal, discVal);
             }
 
             return Math.max(0, lineSubtotal - discount);
         },
 
         calculateSubtotal() {
-            return this.items.reduce((sum, item) => sum + this.calculateLineTotal(item), 0);
+            return (this.items || []).reduce((sum, item) => sum + this.calculateLineTotal(item), 0);
         },
 
         calculateDocDiscount() {
             const subtotal = this.calculateSubtotal();
+            const discVal = Math.max(0, parseFloat(this.discountValue) || 0);
             if (this.discountType === 'percentage') {
-                return subtotal * ((parseFloat(this.discountValue) || 0) / 100);
+                const pct = Math.min(100, discVal);
+                return subtotal * (pct / 100);
             } else if (this.discountType === 'fixed') {
-                return Math.min(subtotal, parseFloat(this.discountValue) || 0);
+                return Math.min(subtotal, discVal);
             }
             return 0;
         },
 
         calculateTaxAmount() {
             const taxable = Math.max(0, this.calculateSubtotal() - this.calculateDocDiscount());
-            const taxPct = parseFloat(this.taxPercentage) || 0;
-            return taxPct > 0 ? (taxable * (taxPct / 100)) : 0;
+            const rawPct = parseFloat(this.taxPercentage);
+            if (isNaN(rawPct) || rawPct <= 0) {
+                return 0;
+            }
+            const taxPct = Math.min(100, rawPct);
+            return (taxable * (taxPct / 100));
         },
 
         calculateGrandTotal() {
             const taxable = Math.max(0, this.calculateSubtotal() - this.calculateDocDiscount());
             const tax = this.calculateTaxAmount();
-            const additional = parseFloat(this.additionalCharges) || 0;
+            const additional = Math.max(0, parseFloat(this.additionalCharges) || 0);
             return Math.max(0, taxable + tax + additional);
         },
 
         formatMoney(amount) {
-            return parseFloat(amount || 0).toLocaleString('en-US', {
+            const num = parseFloat(amount);
+            return (isNaN(num) ? 0 : num).toLocaleString('en-US', {
                 minimumFractionDigits: 2,
                 maximumFractionDigits: 2
             });
         }
```

---

## 4. Alpine.js Binding & Reactivity Verification

1. **Two-Way Binding:**
   - `<input type="number" step="0.01" min="0" max="100" name="tax_percentage" x-model.number="taxPercentage" />` binds synchronously to `taxPercentage`.
   - Any keystroke, stepper click, or programmatic modification immediately triggers dependent Alpine expressions.

2. **Reactive Cascade:**
   - When `taxPercentage` changes, `calculateTaxAmount()` re-computes immediately.
   - The reactive DOM updates both:
     - The inline badge: `x-text="'+ ' + formatMoney(calculateTaxAmount())"`
     - The Grand Total amount: `x-text="formatMoney(calculateGrandTotal())"`
   - When items are added, removed, or their quantities/prices change, `calculateSubtotal()` recalculates, propagating instantaneously to `calculateDocDiscount()`, `calculateTaxAmount()`, and `calculateGrandTotal()`.
   - When Document Discount (`discountType` or `discountValue`) changes, `calculateTaxAmount()` immediately updates because VAT is assessed on the post-discount taxable amount.

---

## 5. Calculation Formulas

The calculations strictly follow standard VAT-exclusive taxation rules:

$$\text{Taxable Subtotal} = \max(0, \text{Subtotal} - \text{Document Discount})$$

$$\text{VAT Amount} = \text{Taxable Subtotal} \times \left(\frac{\text{VAT Percentage}}{100}\right)$$

$$\text{Grand Total} = \text{Taxable Subtotal} + \text{VAT Amount} + \text{Additional Charges}$$

### Benchmark Verification Example:
- **Subtotal:** 10,000.00
- **Document Discount:** 0.00
- **Taxable Subtotal:** 10,000.00
- **VAT Rate:** 18.00%
- **VAT Amount:** $10,000.00 \times 0.18 = 1,800.00$
- **Additional Charges:** 0.00
- **Grand Total:** $10,000.00 - 0.00 + 1,800.00 + 0.00 = 11,800.00$

### Combined Discount & Charges Example:
- **Subtotal:** 10,000.00
- **Document Discount:** 1,000.00
- **Taxable Subtotal:** 9,000.00
- **VAT Rate:** 18.00%
- **VAT Amount:** $9,000.00 \times 0.18 = 1,620.00$
- **Additional Charges:** 200.00
- **Grand Total:** $9,000.00 + 1,620.00 + 200.00 = 10,820.00$

---

## 6. Edge Case Handling

| Edge Case | Input Value | Handled Behavior | Resulting Output |
| :--- | :--- | :--- | :--- |
| **Zero Rate** | `0` or `0.00` | Condition `rawPct <= 0` evaluates to true; returns 0 | VAT: `+ 0.00`, Grand Total = Taxable Subtotal |
| **Empty Input** | `""` or `null` | `parseFloat("")` yields `NaN`; caught by `isNaN(rawPct)`; returns 0 | VAT: `+ 0.00`, Grand Total unaffected |
| **Negative Input** | `-5` | `rawPct <= 0` check prevents negative tax | VAT: `+ 0.00`, Grand Total unaffected |
| **Rate Above 100%** | `125` | `Math.min(100, rawPct)` clamps tax rate to 100.00% | VAT capped at 100% of taxable subtotal |
| **Fractional/Decimal** | `2.5` or `18.5` | Parsed as IEEE 754 float; multiplied accurately | VAT: exact percentage, formatted to 2 decimals |
| **Excessive Discount** | Discount > Subtotal | `Math.max(0, subtotal - discount)` prevents negative taxable base | VAT: `+ 0.00`, Grand Total = Additional Charges |
| **Empty Items List** | No items | Subtotal evaluates to `0.00` | VAT: `+ 0.00`, Grand Total = Additional Charges |

---

## 7. Backend Consistency Verification

Inspection of `app/Http/Controllers/Billing/QuotationController.php` confirms complete symmetry between client-side and server-side logic:

1. **Payload Validation:**
   ```php
   'tax_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
   'additional_charges' => ['nullable', 'numeric', 'min:0'],
   ```
2. **Authoritative Server Recomputation (`QuotationController::store` & `update`):**
   ```php
   $taxableSubtotal = max(0, $subtotal - $docDiscAmount);
   $taxPercentage = (float) ($validated['tax_percentage'] ?? 0);
   $taxAmount = 0.0;

   if ($taxPercentage > 0) {
       $taxAmount = round($taxableSubtotal * ($taxPercentage / 100), 2);
   }

   $additionalCharges = (float) ($validated['additional_charges'] ?? 0);
   $grandTotal = max(0, $taxableSubtotal + $taxAmount + $additionalCharges);
   ```
3. **Database Persistence:**
   The server persists `subtotal`, `discount_amount`, `tax_percentage`, `tax_amount`, `additional_charges`, and `grand_total` into the `quotations` table. Client totals are never blindly trusted.

---

## 8. What Was Not Changed

- **UI Layout & Styling:** No Tailwind layout redesign was performed on the summary card. Existing classes and component boundaries were preserved.
- **Configurability:** No hardcoded tax rate was applied; the field remains dynamically configurable by the user, defaulting to 0 or rehydrated input.
- **Unrelated Modules:** Invoices, Job Cards, Direct Sales, Payroll, Inventory, and Settings controllers and views remained untouched.
- **Database Schema:** No migrations were created or executed; existing decimal columns were leveraged.

---

## 9. Testing Status

> **TESTING STATUS: NOT RUN — requested by user**
>
> In accordance with user rules:
> - Automated test runners (`phpunit`, `pest`, `playwright`, browser automation) were **NOT RUN**.
> - Background development servers (`php artisan serve`) were **NOT RUN**.
> - Live browser sessions were **NOT LAUNCHED**.
> - Code verification and mathematical proofing were executed statically.

---

## 10. Sign-off & Verification Matrix

| Verification Check | Requirement | Status | Verification Note |
| :--- | :--- | :--- | :--- |
| **Alpine Model Binding** | `x-model.number="taxPercentage"` | PASS | Bound to Alpine state variable with numeric casting |
| **Reactivity on Input** | Real-time update of VAT & Grand Total | PASS | Computes via reactive methods `calculateTaxAmount()` & `calculateGrandTotal()` |
| **Single VAT Application** | VAT added exactly once | PASS | Grand Total formula: $\text{Taxable Subtotal} + \text{VAT} + \text{Additional}$ |
| **Decimal Formatting** | Displays 2 decimal places | PASS | Formatted via `formatMoney()` (`toLocaleString` with 2 decimals) |
| **Old Input Rehydration** | Preserves input across failed validation | PASS | Config passes `(float) old('tax_percentage', 0)` |
| **Server-Side Verification** | Authoritative calculation in controller | PASS | Identical logic validated and saved in `QuotationController` |
| **Zero VAT State** | Displays `+ 0.00` when 0% entered | PASS | Expected behavior preserved without treating zero as a bug |
| **Non-Regression** | Unrelated modules preserved | PASS | Limited strictly to quotation view logic |

