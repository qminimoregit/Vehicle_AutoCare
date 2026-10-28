# QA Report: Quotation Calculation Summary UI Alignment & Spacing Fix

**Project:** Viran Auto Care Management System (`D:\Intern\08\autocare-system`)  
**Affected Page:** `http://localhost:8000/billing/quotations/create`  
**Date:** 2026-10-28  
**Scope:** UI / Presentation Layer Only  
**Testing Status:** **NOT RUN — requested by user**

---

## 1. UI Issue

On the Create Quotation page (`/billing/quotations/create`), the **Calculation Summary** card exhibited noticeable visual inconsistencies, field misalignment, and uneven spacing:

- **Vertical & Horizontal Misalignment:** The Discount dropdown was grouped together with the label rather than aligning cleanly with the secondary controls column; inputs had varying widths (`w-24`, `w-20`) and heights (`py-1`, `py-0.5`, `min-h` discrepancies), resulting in uneven right edges.
- **Inconsistent Readout Alignments:** The Subtotal readout lacked a currency prefix and uniform right-column sizing, whereas Grand Total used `LKR` with prominent typography.
- **Tax / VAT Readout Spacing:** The calculated tax amount span was restricted to `w-16` without a currency prefix, causing cramped display against the tax percentage input.
- **Additional Charges Offset:** The Additional Charges input was not vertically aligned with the monetary amount column, producing a jagged right boundary.

---

## 2. Exact Markup Corrected

The Calculation Summary card markup in `resources/views/billing/quotations/create.blade.php` was restructured into a clean 3-column visual layout:

```blade
<!-- Financial Summary Card -->
<div class="bg-white border border-gray-200 rounded-xl p-5 sm:p-6 shadow-xs space-y-3.5">
    <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-500 border-b border-gray-100 pb-2">
        Calculation Summary
    </h2>

    <!-- Subtotal -->
    <div class="flex justify-between items-center min-h-[36px] text-sm">
        <span class="text-xs sm:text-sm font-medium text-gray-600">Subtotal</span>
        <div class="w-28 sm:w-32 h-9 text-right text-xs sm:text-sm font-bold text-gray-900 flex items-center justify-end whitespace-nowrap">
            <span class="text-xs text-gray-400 font-normal mr-1.5">LKR</span>
            <span x-text="formatMoney(calculateSubtotal())"></span>
        </div>
    </div>

    <!-- Document Discount -->
    <div class="flex justify-between items-center min-h-[36px] text-sm">
        <span class="text-xs sm:text-sm font-medium text-gray-600">Discount</span>
        <div class="flex items-center space-x-2 justify-end">
            <select 
                name="discount_type" 
                x-model="discountType"
                class="w-24 sm:w-28 h-9 text-xs border border-gray-300 rounded-lg px-2.5 py-1.5 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-[#0B1849] focus:border-[#0B1849] cursor-pointer"
            >
                <option value="none">None</option>
                <option value="fixed">Fixed (LKR)</option>
                <option value="percentage">Percentage (%)</option>
            </select>
            <input 
                type="number" 
                step="0.01" 
                min="0" 
                name="discount_value" 
                x-model.number="discountValue"
                :disabled="discountType === 'none'"
                placeholder="0.00"
                class="w-28 sm:w-32 h-9 px-3 py-1.5 text-xs text-right border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#0B1849] focus:border-[#0B1849] disabled:bg-gray-100 disabled:text-gray-400"
            />
        </div>
    </div>

    <!-- Tax / VAT Percentage -->
    <div class="flex justify-between items-center min-h-[36px] text-sm">
        <span class="text-xs sm:text-sm font-medium text-gray-600">Tax / VAT (%)</span>
        <div class="flex items-center space-x-2 justify-end">
            <input 
                type="number" 
                step="0.01" 
                min="0" 
                max="100" 
                name="tax_percentage" 
                x-model.number="taxPercentage"
                placeholder="0.00"
                class="w-24 sm:w-28 h-9 px-3 py-1.5 text-xs text-right border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#0B1849] focus:border-[#0B1849]"
            />
            <div class="w-28 sm:w-32 h-9 text-right text-xs font-semibold text-gray-700 flex items-center justify-end whitespace-nowrap">
                <span class="text-gray-400 font-normal mr-1">+ LKR</span>
                <span x-text="formatMoney(calculateTaxAmount())"></span>
            </div>
        </div>
    </div>

    <!-- Additional Charges -->
    <div class="flex justify-between items-center min-h-[36px] text-sm">
        <span class="text-xs sm:text-sm font-medium text-gray-600">Additional Charges</span>
        <div class="flex items-center justify-end">
            <input 
                type="number" 
                step="0.01" 
                min="0" 
                name="additional_charges" 
                x-model.number="additionalCharges"
                placeholder="0.00"
                class="w-28 sm:w-32 h-9 px-3 py-1.5 text-xs text-right border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#0B1849] focus:border-[#0B1849]"
            />
        </div>
    </div>

    <!-- Grand Total -->
    <div class="border-t-2 border-gray-900 pt-3.5 flex justify-between items-baseline">
        <span class="text-base font-black text-gray-900">Grand Total</span>
        <div class="text-right flex items-baseline justify-end whitespace-nowrap">
            <span class="text-xs text-gray-400 font-normal mr-1.5">LKR</span>
            <span class="text-2xl font-black text-[#0B1849]" x-text="formatMoney(calculateGrandTotal())"></span>
        </div>
    </div>

    <!-- Form Submit Action -->
    <div class="pt-3">
        <button 
            type="submit" 
            class="w-full inline-flex items-center justify-center px-6 py-3 bg-[#0B1849] hover:bg-[#15286e] text-white font-bold text-sm rounded-lg shadow-xs transition cursor-pointer min-h-[46px]"
        >
            Save & Preview Quotation
        </button>
    </div>
</div>
```

---

## 3. Files Modified

| File | Type of Change | Scope |
| :--- | :--- | :--- |
| `resources/views/billing/quotations/create.blade.php` | Blade View Template | Local HTML & Tailwind CSS classes inside Calculation Summary card only |

*No controllers, services, database migrations, configuration files, or other view templates were altered.*

---

## 4. Alignment & Spacing Improvements

The new layout realizes the requested ASCII architectural structure:

```
CALCULATION SUMMARY

Subtotal                         LKR 0.00

Discount         [None ▼]          [0.00]

Tax / VAT (%)    [0.00]       + LKR 0.00

Additional Charges               [0.00]

-----------------------------------------

Grand Total                      LKR 0.00

[       Save & Preview Quotation       ]
```

### Specific Improvements:
1. **Left-Column Label Alignment:**
   - All labels (`Subtotal`, `Discount`, `Tax / VAT (%)`, `Additional Charges`, `Grand Total`) are left-aligned using consistent typography (`text-xs sm:text-sm font-medium text-gray-600`).
2. **Middle-Column Secondary Controls Alignment:**
   - Both the Discount dropdown (`[None ▼]`) and Tax / VAT input (`[0.00]`) share identical dimensions: `w-24 sm:w-28` and `h-9` (36px).
   - They align vertically at the exact same horizontal position.
3. **Right-Column Monetary Elements Alignment:**
   - The Discount value input (`[0.00]`), calculated VAT display (`+ LKR 0.00`), and Additional Charges input (`[0.00]`) share identical width: `w-28 sm:w-32` and `h-9` (36px).
   - The Subtotal display and Grand Total display terminate at the exact same right-padded margin.
4. **Uniform Row Heights:**
   - Every row enforces `min-h-[36px]`, eliminating baseline jitter and vertical jumping between static text rows and input rows.
5. **Horizontal & Vertical Spacing:**
   - Row-to-row spacing is harmonized using `space-y-3.5`.
   - Control gaps use `space-x-2` (8px).
   - Input padding is standardized to `px-3 py-1.5`.
6. **Mobile & Desktop Responsiveness:**
   - Total controls group width is bounded between 216px (mobile) and 248px (desktop), preventing wrapping, truncation, or horizontal scrolling on narrow viewports down to 320px.

---

## 5. Confirmation: Financial Calculations Preserved

All financial calculation logic and Alpine.js reactive bindings were strictly preserved without intentional modification:
- `calculateSubtotal()`: Unchanged
- `calculateDocDiscount()`: Unchanged
- `calculateTaxAmount()`: Unchanged
- `calculateGrandTotal()`: Unchanged
- Form field names (`discount_type`, `discount_value`, `tax_percentage`, `additional_charges`): Fully preserved
- Alpine models (`discountType`, `discountValue`, `taxPercentage`, `additionalCharges`): Fully preserved
- Backend validation and persistence: Unaffected

---

## 6. Testing Status

> **TESTING STATUS: NOT RUN — requested by user**
>
> In accordance with the user instructions:
> - Google Chrome was **NOT OPENED**.
> - Playwright was **NOT RUN**.
> - Laravel server (`php artisan serve`) was **NOT STARTED**.
> - Artisan commands were **NOT RUN**.
> - PHPUnit and Pest test suites were **NOT RUN**.
> - NPM commands were **NOT RUN**.
> - Database queries and migrations were **NOT RUN**.
> - No test records were created.

