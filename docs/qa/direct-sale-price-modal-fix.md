# Viran Auto Care — Direct Sale Price Edit Popup Implementation Report

**Document Date:** October 28, 2026  
**System Module:** Sales & Billing — Direct Sale (POS) (`/operations/direct-sales`)  
**Developer Role:** Senior Laravel Blade, Tailwind CSS and Alpine.js Developer  
**Implementation Status:** **IMPLEMENTED (UNTESTED — NO EXECUTION AS REQUESTED)**

---

## 1. Requested Change

In the Direct Sale (POS) cart interface (`/operations/direct-sales`), replace the text-based inline price toggle:
```
Toyota Genuine Engine Oil Filter  
SKU: FLT-TOY-001 — Edit Price / Close Price
```
with an accessible, single pencil icon placed beside the product SKU:
```
Toyota Genuine Engine Oil Filter  
SKU: FLT-TOY-001 — [Pencil Icon]
```
When clicked, instead of expanding an inline section that stretches cart rows, open a centered modal popup matching the visual design, layout, and calculations of the Job Card "Edit Part" popup (`resources/views/operations/jobs/show.blade.php`).

---

## 2. Existing Implementation Inspected

1. **Job Card Reference Popup (`resources/views/operations/jobs/show.blade.php`):**
   - Modal container: Centered backdrop overlay with dark navy tint (`bg-[#0D1946]/45 backdrop-blur-xs`), white rounded container (`rounded-2xl shadow-2xl border border-gray-200`).
   - Header: Dark navy bold title (`text-[#0D1946]`) with icon and close button (`bi bi-x-lg`).
   - Product Details Panel: Rounded grey container (`bg-gray-50 border border-gray-200 rounded-xl`) showing Product Name, SKU, and Quantity.
   - Form Controls: Unit Selling Price input with `LKR` prefix indicator and helper text.
   - Cost & Margin Panel: Rounded slate container (`bg-slate-50 border border-slate-200 rounded-xl`) dynamically displaying Cost Price, New Selling Price, Estimated Unit Profit, Estimated Total Profit, Profit Margin (%), and Below-Cost Estimated Loss warning alert.
   - Actions: `Cancel` and `Save Changes` buttons in a light grey footer (`bg-gray-50 border-t border-gray-100`).
   - Pencil Trigger Button: Styled via `.lv-edit-btn` (`width: 22px; height: 22px; border-radius: 6px;`).

2. **Existing Direct Sale Implementation (`resources/views/operations/direct-sales/index.blade.php`):**
   - Had inline text toggle button `<span x-text="item.showPriceEdit ? 'Close Price' : 'Edit Price'"></span>`.
   - Expanded an inline drawer inside each cart row (`x-show="item.showPriceEdit"`), distorting the compact row layout.
   - Live updated cart prices immediately upon keystroke via `@input="updateItemPrice(index)"`, leaving no clean cancel/discard mechanism.

---

## 3. Files Modified

In strict compliance with file modification restrictions:
- **Modified:** `resources/views/operations/direct-sales/index.blade.php`
- **No other files were created, edited, moved, or deleted.**

---

## 4. Pencil Icon Implementation

1. **Replaced Text with Single Pencil Icon:**  
   Removed the text `"Edit Price"` / `"Close Price"` and replaced it with a compact button hosting `<i class="bi bi-pencil"></i>`.
2. **Placement beside SKU:**  
   Positioned next to the product SKU separated by an em-dash (`—`):
   ```blade
   <div class="flex items-center gap-1.5 mt-0.5">
       <div class="lv-line-tech" x-text="'SKU: ' + (item.sku || 'N/A')"></div>
       @if(Auth::user()->canViewCostAndProfit())
           <span class="text-gray-300 text-xs">—</span>
           <button 
               type="button" 
               @click="openPriceEdit(index)" 
               class="lv-edit-btn"
               title="Edit selling price"
               aria-label="Edit selling price"
           >
               <i class="bi bi-pencil"></i>
           </button>
       @endif
   </div>
   ```
3. **CSS Styling (`.lv-edit-btn`):**  
   Added compact, non-intrusive styling matching the Job Card design system:
   ```css
   .lv-edit-btn {
       flex: 0 0 auto;
       border: 0;
       background: none;
       color: #94A3B8;
       width: 22px;
       height: 22px;
       border-radius: 6px;
       display: inline-flex;
       align-items: center;
       justify-content: center;
       cursor: pointer;
       font-size: 11px;
       transition: all 0.15s;
   }
   .lv-edit-btn:hover {
       background: #EFF6FF;
       color: #2563EB;
   }
   ```
4. **Duplicate Control Elimination:**  
   Removed the old inline expandable drawer (`x-show="item.showPriceEdit"`) from cart rows, preserving clean row spacing and eliminating redundant controls.

---

## 5. Popup Structure and Styling

The modal is centered and positioned at the root of the Alpine component, styled to match the Job Card "Edit Part" popup:

1. **Backdrop & Window:**
   - Overlay: `fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#0D1946]/45 backdrop-blur-xs`
   - Dialog: `bg-white rounded-2xl w-full max-w-sm sm:max-w-md shadow-2xl border border-gray-200 overflow-hidden relative z-60`
2. **Header:**
   - Dark navy heading: `<h3 class="text-base font-extrabold text-[#0D1946] m-0 flex items-center gap-2">`
   - Icon: `<i class="bi bi-pencil-square text-blue-600"></i>`
   - Close (X) button with `@click="cancelPriceEdit()"`.
3. **Product Details Panel:**
   - Background: `bg-gray-50 border border-gray-200 rounded-xl p-3 text-xs`
   - Displays Product Name (`editingPriceItem.name`), SKU (`editingPriceItem.sku`), and formatted Quantity (`formatQty(editingPriceItem.quantity)`).
4. **Price Input Field:**
   - Label: `Unit Selling Price (LKR) *`
   - Input: `<input type="number" x-model.number="editingPriceItem.unit_price" min="0" step="0.01" ...>` with `LKR` prefix adornment and Enter key trigger (`@keydown.enter.prevent="savePriceEdit()"`).
5. **Profit Analysis Section:**
   - Background: `bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs`
   - Dynamically updates as the selling price is typed.
6. **Footer:**
   - Cancel button (`@click="cancelPriceEdit()"`).
   - Save Changes button (`@click="savePriceEdit()"`).

---

## 6. Profit Calculation Logic

Calculations are computed reactively in Alpine without modifying inventory costs or global product prices:

- **Unit Profit:**
  ```javascript
  Unit Profit = Number(editingPriceItem.unit_price) - editingPriceItem.unit_cost
  ```
- **Estimated Total Profit:**
  ```javascript
  Estimated Total Profit = (Number(editingPriceItem.unit_price) - editingPriceItem.unit_cost) * editingPriceItem.quantity
  ```
  *(Rendered dynamically whenever `quantity > 1`)*
- **Profit Margin (%):**
  ```javascript
  Margin = Number(editingPriceItem.unit_price) > 0 
      ? (((Number(editingPriceItem.unit_price) - editingPriceItem.unit_cost) / Number(editingPriceItem.unit_price)) * 100).toFixed(2) + '%' 
      : '0.00%'
  ```
  *(Division by zero is guarded; when selling price is 0 or negative, margin displays `0.00%`)*
- **Cost Price Display & Availability:**
  - When `unit_cost !== null && unit_cost !== undefined`: Formatted as `LKR X,XXX.XX`.
  - When null or undefined: Displays italicized amber badge `Cost unavailable`.
- **Below Cost Loss Indicator:**
  - Evaluated via `(Number(editingPriceItem.unit_price) - editingPriceItem.unit_cost) < 0`.
  - Numbers render in red (`text-red-600`) instead of emerald (`text-emerald-700`).
  - Displays a warning alert badge:
    ```blade
    <div class="mt-1 px-2.5 py-1.5 bg-red-100 text-red-700 font-bold rounded-lg text-xs flex items-center gap-1.5">
        <i class="bi bi-exclamation-triangle-fill text-red-600"></i>
        <span>Below Cost — Estimated Loss</span>
    </div>
    ```

---

## 7. Save and Cancel Behaviour

### A. Open Modal (`openPriceEdit(index)`)
- Reads the selected item from `cart[index]`.
- Creates an isolated staging object in Alpine:
  ```javascript
  this.editingItemIndex = index;
  this.editingPriceItem = {
      name: item.name,
      sku: item.sku || 'N/A',
      quantity: item.quantity || 1,
      unit_price: currentPrice,
      original_price: currentPrice,
      unit_cost: (item.unit_cost !== null && item.unit_cost !== undefined) ? Number(item.unit_cost) : null,
  };
  this.showPriceModal = true;
  ```

### B. Save Changes (`savePriceEdit()`)
1. Validates `editingPriceItem.unit_price >= 0` and is numeric.
2. Updates **only** `cart[editingItemIndex].unit_price`.
3. Recalculates item line total:
   `item.line_total = Math.round(item.quantity * item.unit_price * 100) / 100;`
4. Calls `this.recalculatePayment()`, updating subtotal, discount, and grand total.
5. Closes modal (`showPriceModal = false`).
6. Cart row displays the newly saved unit price and line total.
7. Does **not** mutate product catalog, inventory records, or database rows.

### C. Cancel (`cancelPriceEdit()`)
- Triggered by clicking **Cancel**, the **Close (X)** button, clicking the backdrop overlay, or pressing **Escape** (`@keydown.escape.window="cancelPriceEdit()"`).
- Resets `showPriceModal = false` and `editingItemIndex = null`.
- Because edits were made to `editingPriceItem` in staging, `cart[index].unit_price` remains completely untouched.
- Restores original cart pricing and preserves all cart quantities, customer info, and discounts.

---

## 8. Permission Handling

- Enforced through Laravel Blade authorization check:
  `@if(Auth::user()->canViewCostAndProfit())`
- **Unauthorized users** (cashiers or staff without margin permissions):
  - Will not see the pencil icon in the cart row.
  - Will not have the Edit Price modal rendered into their DOM.
  - Will never receive sensitive inventory unit cost or margin metrics.
- **Authorized users** (Supervisors and Administrators):
  - View the pencil icon.
  - Can open the modal to adjust cart item selling prices and analyze real-time FIFO margins.

---

## 9. Confirmation of Module Isolation

- **Job Cards:** Untouched (`resources/views/operations/jobs/*` remains unmodified).
- **Invoices & Billing:** Untouched.
- **Quotations:** Untouched.
- **Inventory & Stocks:** Untouched (no database adjustments or stock deductions).
- **Global CSS / JS:** No new global CSS files added; styles are localized within the direct sales template.
- **Database:** No migrations, schema changes, or database modifications executed.

---

## 10. Testing Status: NOT RUN — As Requested

In strict accordance with **Rule 9 ("DO NOT TEST OR RUN ANYTHING")**:
- **Google Chrome / Browser:** NOT opened.
- **Playwright / Automation:** NOT run.
- **Application Server (`php artisan serve`):** NOT started.
- **Test Suites (PHPUnit / Pest / `php artisan test`):** NOT run.
- **Database Queries / Seeders:** NOT executed.
- **Frontend Build Tools (`npm run dev` / `npm run build`):** NOT run.
- **Background Processes:** None started.

Implementation was completed purely through static inspection and precise code authoring.

---

## 11. Assumptions & Limitations

1. **Asset Compilation:** The implementation uses existing Tailwind CSS utilities and existing Bootstrap Icons (`bi bi-pencil`, `bi bi-pencil-square`, `bi bi-x-lg`, `bi bi-check2`, `bi bi-exclamation-triangle-fill`) already bundled in the application. No rebuild is necessary.
2. **Permission Boundary:** Relies on the established `Auth::user()->canViewCostAndProfit()` method consistently used across Job Cards and Direct Sales.
3. **Cart Item Scope:** Price changes made through this modal apply strictly to the active transaction item in the POS cart and do not alter product master prices.

