# Viran Auto Care — Invoice Record Payment Modal Auto-Close & Idempotency Fix Report

**Document Date:** October 28, 2026  
**System Module:** Sales & Billing — Invoice Payments (`/operations/invoices/{id}`)  
**Engineer Role:** Senior Laravel 13, Blade, Alpine.js & Browser Automation QA Engineer  
**Status:** **RESOLVED & VERIFIED IN LIVE GOOGLE CHROME**

---

## 1. Root Cause Analysis

Prior to this fix, submitting a payment for an invoice resulted in two primary UX/architectural defects:

1. **Failure to Auto-Close on Successful Payment:**
   - On the Invoices Index page (`resources/views/operations/invoices/index.blade.php`), the "Record Payment" button links to:
     ```blade
     href="{{ route('operations.invoices.show', $invoice) }}?action=pay"
     ```
   - In `resources/views/operations/invoices/show.blade.php` (line 34), Alpine initialized the payment modal state as:
     ```blade
     showPaymentModal: {{ (request()->query('action') === 'pay' && $invoice->balance_amount > 0 && $invoice->status !== 'cancelled') ? 'true' : 'false' }}
     ```
   - When the administrator submitted a payment, `Billing\PaymentController@store` returned:
     ```php
     return back()->with('success', ...);
     ```
   - Because `back()` redirects to the HTTP referrer URL, the query string `?action=pay` was preserved in the browser's address bar.
   - Upon page reload, `request()->query('action') === 'pay'` evaluated to `true`. If the invoice still had a remaining balance (partial payment), `$invoice->balance_amount > 0` was also `true`.
   - Consequently, Alpine re-initialized `showPaymentModal` to `true`, forcing the "Record Payment" modal to reopen immediately after the payment was saved, obscuring the invoice and creating confusion as to whether the payment had succeeded.

2. **Premature Modal Closure on Error:**
   - If an error occurred (e.g. overpayment, validation error, or credit limit rejection), `back()->withErrors()->withInput()` returned to the invoice URL without `?action=pay` (if opened via the in-page button `@click="showPaymentModal = true"`).
   - Because `request()->query('action') === 'pay'` evaluated to `false`, the modal was closed prematurely, hiding the error and discarding the user's form context.
   - Furthermore, there was no designated error block rendered inside the modal body, meaning errors could only be seen if the user reopened the form or looked outside the backdrop.

3. **Risk of Rapid Duplicate Submissions:**
   - Double-clicking the "Save Payment" button or rapid retries could dispatch multiple HTTP POST requests to `billing.payments.store` within a sub-second interval before browser navigation began, causing duplicate financial entries.

---

## 2. Files Modified

In strict adherence to project boundaries, modifications were restricted **only** to the two files directly responsible for the invoice Record Payment modal and its HTTP lifecycle:

1. `app/Http/Controllers/Billing/PaymentController.php`
2. `resources/views/operations/invoices/show.blade.php`

---

## 3. Code Changes (Diffs)

### A. `app/Http/Controllers/Billing/PaymentController.php`

1. **Clean Route Redirect on Success:**  
   Replaced `back()` with an explicit named redirect to `route('operations.invoices.show', $invoice)`. This strips any query parameters (such as `?action=pay`) from the URL upon successful payment save.
2. **Duplicate Payment Guard:**  
   Added database-level deduplication inside the transaction locking block. Any identical payment request (matching `invoice_id`, `amount`, `payment_method`, and `created_by` within the past 15 seconds) is intercepted before record insertion.
3. **Preserved Input on Credit Rejection:**  
   Appended `withInput()` to credit account validation failure redirects.

```diff
--- a/app/Http/Controllers/Billing/PaymentController.php
+++ b/app/Http/Controllers/Billing/PaymentController.php
@@ -155,12 +155,12 @@ public function store(Request $request, Invoice $invoice): RedirectResponse
             $creditAccount = $customer?->creditAccount()->where('status', 'active')->first();
 
             if (! $creditAccount) {
-                return back()->with('error', 'Customer does not have an active approved credit account.');
+                return back()->withInput()->with('error', 'Customer does not have an active approved credit account.');
             }
 
             $payAmount = round((float) $validated['amount'], 2);
             if ($creditAccount->wouldExceedLimit($payAmount) && ! $user->isAdmin()) {
-                return back()->with('error', 'Company credit limit exceeded. Available credit: LKR '.number_format($creditAccount->available_credit, 2).'. Admin override required.');
+                return back()->withInput()->with('error', 'Company credit limit exceeded. Available credit: LKR '.number_format($creditAccount->available_credit, 2).'. Admin override required.');
             }
         }
 
@@ -187,6 +187,18 @@ public function store(Request $request, Invoice $invoice): RedirectResponse
                     );
                 }
 
+                // Prevent duplicate payment records caused by double-clicking or repeated rapid submissions
+                $duplicatePayment = Payment::where('invoice_id', $freshInvoice->id)
+                    ->where('amount', $payAmount)
+                    ->where('payment_method', $validated['payment_method'])
+                    ->where('created_by', $user->id)
+                    ->where('created_at', '>=', now()->subSeconds(15))
+                    ->first();
+
+                if ($duplicatePayment) {
+                    throw new Exception('A payment of LKR '.number_format($payAmount, 2)." was just recorded (#{$duplicatePayment->payment_number}). Duplicate submission prevented.");
+                }
+
                 $paymentNumber = Payment::generatePaymentNumber();
 
                 $payment = Payment::create([
@@ -233,7 +245,8 @@ public function store(Request $request, Invoice $invoice): RedirectResponse
                 return $payment;
             });
 
-            return back()->with('success', "Payment {$payment->payment_number} of LKR ".number_format($payment->amount, 2).' recorded successfully.')
+            return redirect()->route('operations.invoices.show', $invoice)
+                ->with('success', "Payment {$payment->payment_number} of LKR ".number_format($payment->amount, 2).' recorded successfully.')
                 ->with('recent_payment_id', $payment->id);
         } catch (Exception $e) {
             return back()->withInput()->with('error', "Payment failed: {$e->getMessage()}");
```

---

### B. `resources/views/operations/invoices/show.blade.php`

1. **Refined Modal Initialization Condition:**  
   Ensured `showPaymentModal` evaluates to `false` when `session('success')` exists, and evaluates to `true` when `$errors->any()` or `session('error')` exists.
2. **Field Value Retention:**  
   Wrapped default form state with `old(...)` helpers to preserve user input on failure.
3. **In-Modal Error Feedback:**  
   Added dedicated error alerts inside the modal container so administrators see immediate validation/rejection details.
4. **Client-Side Double-Submission Prevention:**  
   Hardened `@submit` with `if (submitting) { $event.preventDefault(); return false; } submitting = true;` and updated button state label to `Processing Payment...`.
5. **History Cleanup:**  
   Added `x-init` hook to replace browser history state and strip `?action=pay` if present on success.

```diff
--- a/resources/views/operations/invoices/show.blade.php
+++ b/resources/views/operations/invoices/show.blade.php
@@ -31,7 +31,7 @@
 
 <div 
     x-data="{ 
-        showPaymentModal: {{ (request()->query('action') === 'pay' && $invoice->balance_amount > 0 && $invoice->status !== 'cancelled') ? 'true' : 'false' }},
+        showPaymentModal: {{ (!session('success') && ($errors->any() || session('error') || request()->query('action') === 'pay') && $invoice->balance_amount > 0 && $invoice->status !== 'cancelled') ? 'true' : 'false' }},
         showWhatsAppModal: false,
         whatsappPhone: '{{ $normPhone }}',
         whatsappText: {{ json_encode($waInvoiceMessage) }},
@@ -55,11 +55,11 @@
             window.open(url, '_blank');
         },
 
-        paymentAmount: '{{ (float)$invoice->balance_amount }}',
-        paymentMethod: 'cash',
-        paymentDate: '{{ date('Y-m-d') }}',
-        refNumber: '',
-        notes: '',
+        paymentAmount: '{{ old('amount', (float)$invoice->balance_amount) }}',
+        paymentMethod: '{{ old('payment_method', 'cash') }}',
+        paymentDate: '{{ old('payment_date', date('Y-m-d')) }}',
+        refNumber: '{{ old('reference_number', '') }}',
+        notes: '{{ old('notes', '') }}',
         submitting: false,
         showReverseModal: false,
         reversingPaymentId: null,
@@ -80,6 +80,13 @@
             this.showReverseModal = true;
         }
     }"
+    x-init="
+        if (window.location.search.includes('action=pay')) {
+            @if(session('success'))
+                window.history.replaceState({}, document.title, window.location.pathname);
+            @endif
+        }
+    "
     class="max-w-4xl mx-auto space-y-6"
 >
@@ -601,12 +608,39 @@
                     </div>
                 </div>
 
+                @if($errors->any())
+                    <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 space-y-1">
+                        <div class="font-bold flex items-center">
+                            <svg class="w-4 h-4 mr-1 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
+                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
+                            </svg>
+                            Payment could not be processed:
+                        </div>
+                        <ul class="list-disc list-inside pl-1 text-[11px] space-y-0.5">
+                            @foreach($errors->all() as $error)
+                                <li>{{ $error }}</li>
+                            @endforeach
+                        </ul>
+                    </div>
+                @endif
+
+                @if(session('error'))
+                    <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 flex items-start">
+                        <svg class="w-4 h-4 mr-1.5 text-red-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
+                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
+                        </svg>
+                        <div>
+                            <span class="font-bold">Error:</span> {{ session('error') }}
+                        </div>
+                    </div>
+                @endif
+
                 <!-- Payment Form -->
                 <form 
                     action="{{ route('billing.payments.store', $invoice) }}" 
                     method="POST" 
                     class="space-y-4"
-                    @submit="submitting = true"
+                    @submit="if (submitting) { $event.preventDefault(); return false; } submitting = true;"
                 >
                     @csrf
@@ -708,7 +742,7 @@
                                 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                             </svg>
-                            Save Payment
+                            <span x-text="submitting ? 'Processing Payment...' : 'Save Payment'">Save Payment</span>
                         </button>
                     </div>
                 </form>
```

---

## 4. Success Modal Close Behaviour

When a payment is successfully saved:
1. `PaymentController@store` redirects to `route('operations.invoices.show', $invoice)`.
2. The HTTP response contains a flash session: `session('success', "Payment PAY-... recorded successfully.")`.
3. In `show.blade.php`, `showPaymentModal` evaluates `!session('success')`, which resolves to `false`.
4. Alpine initializes with `showPaymentModal: false`.
5. The popup remains completely closed and hidden (`display: none;`).
6. The emerald notification banner `Payment PAY-... recorded successfully.` is rendered prominently at the top of the invoice page.
7. The Total Paid amount increases, the Outstanding Balance decreases, and the payment appears immediately in the Payments history table.
8. If the balance reaches LKR 0.00, the status badge updates to `Payment: Paid in Full` and the "Record Payment" button is replaced by the green `Paid in Full` pill.

---

## 5. Error Modal Retention Behaviour

When a payment fails validation or business logic (e.g. overpayment, company credit limit exceeded, duplicate submission, or invalid date):
1. `PaymentController@store` catches the exception and returns `back()->withInput()->with('error', "Payment failed: {$e->getMessage()}")`.
2. In `show.blade.php`, `session('error')` is truthy and `!session('success')` is truthy.
3. Alpine initializes with `showPaymentModal: true`.
4. The popup **remains open** automatically after the page reload without closing prematurely.
5. A styled alert banner is displayed inside the modal:
   ```
   [!] Error: Payment failed: Payment amount (LKR 25,000.00) cannot exceed the outstanding balance of LKR 10,000.00.
   ```
6. The entered values (amount, payment method, date, reference, notes) are preserved in the input fields via `old(...)`, allowing the administrator to make adjustments without re-entering data.

---

## 6. Duplicate Payment Prevention

To eliminate accidental duplicate transactions caused by rapid double-clicks or network retries:
1. **Frontend Level:**  
   The `<form>` listener checks `submitting`:
   `@submit="if (submitting) { $event.preventDefault(); return false; } submitting = true;"`.  
   The submit button disables itself and shows a spinner with `Processing Payment...`.
2. **Backend Database Level:**  
   Inside the database transaction (protected by row-level locking `lockForUpdate()`), the controller checks for any existing payment matching the same `invoice_id`, `amount`, `payment_method`, and `created_by` created within the last 15 seconds:
   ```php
   $duplicatePayment = Payment::where('invoice_id', $freshInvoice->id)
       ->where('amount', $payAmount)
       ->where('payment_method', $validated['payment_method'])
       ->where('created_by', $user->id)
       ->where('created_at', '>=', now()->subSeconds(15))
       ->first();
   ```
   If found, the transaction rolls back immediately and throws:
   `"A payment of LKR ... was just recorded (#...). Duplicate submission prevented."`
3. No duplicate financial entry, payment row, or Money Book record is created.

---

## 7. Chrome Test Results

Testing was conducted live in Google Chrome on the active Windows desktop via CDP (`http://127.0.0.1:9333`):

| Test Case | Scenario Description | Expected Outcome | Result | Verification Screenshot |
|:---:|:---|:---|:---:|:---|
| **TC1 & TC2** | Open invoice with outstanding balance & click "Record Payment". | Modal opens with customer info, invoice total, already paid, outstanding balance, and amount input defaulting to balance. | **PASS** | `TC1_TC2_modal_opened.png` |
| **TC4** | Submit invalid payment (overpayment LKR 25,000.00 exceeding LKR 10,000.00 balance). | Modal remains open; error banner rendered inside modal; entered value 25000.00 preserved; no false success message. | **PASS** | `TC4_error_modal_remains_open.png` |
| **TC3** | Submit valid partial payment (LKR 1,000.00 Cash). | Modal closes automatically; success alert appears; Paid Amount increases to LKR 6,500.00; Balance drops to LKR 9,000.00; `action=pay` stripped from URL. | **PASS** | `TC3_modal_closed_payment_recorded.png` |
| **TC5** | Rapid duplicate submission attempt (identical LKR 500.00 payment within 2 seconds). | Backend intercepts duplicate; throws duplicate warning; modal stays open with error message; only 1 payment created. | **PASS** | `TC5_duplicate_prevention_alert.png` |
| **TC3b** | Final settlement payment (LKR 7,500.00 to bring balance to 0.00). | Modal closes automatically; status changes to `Payment: Paid in Full`; balance reaches LKR 0.00; "Record Payment" button replaced with `Paid in Full` badge. | **PASS** | `TC3b_paid_in_full_header.png`, `TC3b_paid_in_full_summary.png` |
| **TC6** | Database audit & integrity verification. | Payments table contains correct rows; Invoices row reflects exact totals; Activity logs reflect audit entries. | **PASS** | Verified via `database-query` |

---

## 8. Invoices Tested

- **Target Invoice:** Invoice ID `78` (`INV-202610-0012`)
- **Customer:** Chamara Wijesinghe (Vehicle: `WP CAB-1902`)
- **Branch:** Viran Auto Care Mattegoda

---

## 9. Payment IDs Generated & Traceability

| Payment ID | Payment Number | Amount (LKR) | Method | Timestamp (UTC) | Purpose / Test Step |
|:---:|:---:|:---:|:---:|:---:|:---|
| `69` | `PAY-202610-0006` | 5,000.00 | Cash | 2026-10-28 04:55:10 | Pre-existing baseline payment |
| `70` | `PAY-202610-0007` | 500.00 | Cash | 2026-10-28 04:55:27 | Pre-existing baseline payment |
| `72` | `PAY-202610-0009` | 1,000.00 | Cash | 2026-10-28 05:28:16 | TC3 Partial payment test (Auto-close verified) |
| `74` | `PAY-202610-0011` | 1,000.00 | Cash | 2026-10-28 05:29:11 | Secondary partial payment verification |
| `75` | `PAY-202610-0012` | 500.00 | Cash | 2026-10-28 05:30:20 | TC5 Initial payment for rapid duplicate test |
| `76` | `PAY-202610-0013` | 7,500.00 | Cash | 2026-10-28 05:31:36 | TC3b Final settlement to reach Paid in Full |

---

## 10. Final Financial Balances (Invoice 78)

- **Grand Total:** LKR 15,500.00
- **Total Amount Paid:** LKR 15,500.00 (`5000 + 500 + 1000 + 1000 + 500 + 7500`)
- **Outstanding Balance:** **LKR 0.00**
- **Document Status:** `Issued`
- **Payment Status:** `Paid in Full` (`paid`)

Database record inspection:
```json
{
  "id": 78,
  "invoice_number": "INV-202610-0012",
  "grand_total": "15500.00",
  "amount_paid": "15500.00",
  "balance_amount": "0.00",
  "status": "paid"
}
```

Audit log verification (`activity_logs`):
```json
{
  "action": "payment_recorded",
  "description": "Recorded payment PAY-202610-0013 of LKR 7,500.00 for Invoice #INV-202610-0012. New balance: LKR 0.00.",
  "module": "Sales & Billing"
}
```

---

## 11. Verification Steps for Reproduction

To verify the fix in any browser environment:

1. **Verify Auto-Close on Success:**
   - Navigate to `/operations/invoices/{id}` with an unpaid/partially-paid invoice.
   - Click **Record Payment**.
   - Enter an amount equal to or less than the outstanding balance.
   - Click **Save Payment**.
   - Observe that upon redirect, the modal is **completely closed**, the success notification appears at the top, the balance is updated, and the URL does not retain `?action=pay`.

2. **Verify Modal Retention on Error:**
   - Open the **Record Payment** modal on an invoice with an outstanding balance.
   - Attempt an overpayment amount exceeding the balance (or enter an invalid date).
   - Submit the form.
   - Observe that upon reload, the modal **remains open**, the error alert appears inside the modal body, and the input value is preserved.

3. **Verify Duplicate Submission Block:**
   - Submit a payment.
   - Within 15 seconds, attempt to submit an identical payment for the same amount and method.
   - Observe that the second submission is rejected with:  
     `"A payment of LKR ... was just recorded (#...). Duplicate submission prevented."`  
     The modal remains open showing the duplicate warning, and no duplicate database record is created.

