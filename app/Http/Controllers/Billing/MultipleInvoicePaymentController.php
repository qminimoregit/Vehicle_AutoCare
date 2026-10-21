<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\PaymentReceipt;
use App\Services\BranchContext;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MultipleInvoicePaymentController extends Controller
{
    /**
     * Display the Multiple Invoice Payment screen (Split Payment & Bulk Payment tabs).
     */
    public function index(Request $request): View
    {
        $activeBranchId = BranchContext::getActiveBranchId();
        $selectedCustomerId = $request->query('customer_id');

        // Load customers that have at least one issued or partially paid invoice
        $customersQuery = Customer::where('status', 'active')
            ->whereHas('invoices', function ($q) use ($activeBranchId) {
                $q->whereIn('status', ['issued', 'partially_paid'])
                    ->where('balance_amount', '>', 0);
                if ($activeBranchId) {
                    $q->where(function ($sub) use ($activeBranchId) {
                        $sub->where('branch_id', $activeBranchId)->orWhereNull('branch_id');
                    });
                }
            })
            ->withCount(['invoices as unpaid_invoices_count' => function ($q) use ($activeBranchId) {
                $q->whereIn('status', ['issued', 'partially_paid'])
                    ->where('balance_amount', '>', 0);
                if ($activeBranchId) {
                    $q->where(function ($sub) use ($activeBranchId) {
                        $sub->where('branch_id', $activeBranchId)->orWhereNull('branch_id');
                    });
                }
            }])
            ->orderBy('customer_name');

        $customers = $customersQuery->get();

        // If a customer_id is requested but not in the list, try to include them
        if ($selectedCustomerId && ! $customers->contains('id', (int) $selectedCustomerId)) {
            $extraCust = Customer::withCount(['invoices as unpaid_invoices_count' => function ($q) use ($activeBranchId) {
                $q->whereIn('status', ['issued', 'partially_paid'])
                    ->where('balance_amount', '>', 0);
                if ($activeBranchId) {
                    $q->where(function ($sub) use ($activeBranchId) {
                        $sub->where('branch_id', $activeBranchId)->orWhereNull('branch_id');
                    });
                }
            }])->find($selectedCustomerId);

            if ($extraCust) {
                $customers->prepend($extraCust);
            }
        }

        // Optional pre-selected invoice search (normalized)
        $rawInvoiceSearch = trim((string) $request->query('invoice_number', ''));
        $splitInvoice = null;

        if ($rawInvoiceSearch !== '') {
            $normSearch = ltrim($rawInvoiceSearch, " \t\n\r\0\x0B#");
            $normSearch = trim($normSearch);

            $candidates = array_unique(array_filter([
                $rawInvoiceSearch,
                $normSearch,
                '#' . $normSearch,
                strtoupper($normSearch),
                '#' . strtoupper($normSearch),
            ]));

            $splitQuery = Invoice::with(['customer', 'jobCard.vehicle', 'payments'])
                ->where(function ($q) use ($candidates, $normSearch) {
                    $q->whereIn('invoice_number', $candidates)
                      ->orWhereRaw('LOWER(invoice_number) = ?', [strtolower($normSearch)]);
                });

            if ($activeBranchId) {
                $splitQuery->where(function ($q) use ($activeBranchId) {
                    $q->where('branch_id', $activeBranchId)->orWhereNull('branch_id');
                });
            }
            $splitInvoice = $splitQuery->first();
        }

        $initialTab = 'split';
        if ($request->query('tab') === 'multiple' || $request->query('tab') === 'bulk' || $request->has('customer_id')) {
            $initialTab = 'bulk';
        }

        return view('billing.multiple-invoice-payment.index', compact(
            'customers',
            'splitInvoice',
            'rawInvoiceSearch',
            'initialTab',
            'selectedCustomerId'
        ));
    }

    /**
     * JSON search endpoint for Option 1: fetch single invoice details and outstanding balance.
     * Supports both 'INV-...' and '#INV-...' formats, whitespace normalization, and branch isolation.
     */
    public function searchInvoice(Request $request): JsonResponse
    {
        $rawNumber = trim((string) $request->query('number', ''));
        if ($rawNumber === '') {
            return response()->json(['success' => false, 'message' => 'Please enter an invoice number.'], 400);
        }

        $user = Auth::user();
        $activeBranchId = BranchContext::getActiveBranchId();

        // Normalize: strip leading '#' and clean spaces
        $normalized = ltrim($rawNumber, " \t\n\r\0\x0B#");
        $normalized = trim($normalized);

        $candidates = array_unique(array_filter([
            $rawNumber,
            $normalized,
            '#' . $normalized,
            strtoupper($normalized),
            '#' . strtoupper($normalized),
        ]));

        // Check if invoice exists anywhere in the system first
        $invoice = Invoice::with(['customer', 'jobCard.vehicle'])
            ->where(function ($q) use ($candidates, $normalized) {
                $q->whereIn('invoice_number', $candidates)
                  ->orWhereRaw('LOWER(invoice_number) = ?', [strtolower($normalized)]);
            })
            ->first();

        if (! $invoice) {
            return response()->json([
                'success' => false,
                'message' => "Invoice '{$rawNumber}' not found.",
            ], 404);
        }

        // Branch authorization check
        if ($invoice->branch_id && $user && ! $user->canAccessBranch($invoice->branch_id)) {
            return response()->json([
                'success' => false,
                'message' => "You are not authorized to view or collect payments for this invoice branch.",
            ], 403);
        }

        // Check active branch scope if set and not admin
        if ($activeBranchId && $invoice->branch_id && (int) $invoice->branch_id !== (int) $activeBranchId && (! $user || ! $user->isAdmin())) {
            return response()->json([
                'success' => false,
                'message' => "Invoice '{$rawNumber}' belongs to another branch under your account scope.",
            ], 403);
        }

        if ($invoice->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => "Invoice '{$invoice->invoice_number}' is cancelled and cannot accept payments.",
            ], 422);
        }

        if ($invoice->status === 'paid' || (float) $invoice->balance_amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => "Invoice '{$invoice->invoice_number}' is already fully settled.",
            ], 422);
        }

        return response()->json([
            'success' => true,
            'invoice' => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'invoice_date' => $invoice->invoice_date ? $invoice->invoice_date->format('d M Y') : '—',
                'customer_name' => $invoice->customer?->customer_name ?? 'N/A',
                'company_name' => $invoice->customer?->company_name ?? null,
                'vehicle_number' => $invoice->jobCard?->vehicle?->vehicle_number ?? 'N/A',
                'grand_total' => (float) $invoice->grand_total,
                'amount_paid' => (float) $invoice->amount_paid,
                'balance_amount' => (float) $invoice->balance_amount,
                'status' => $invoice->status,
            ],
        ]);
    }

    /**
     * JSON endpoint for Option 2: fetch all unpaid invoices for a given customer within branch scope.
     */
    public function customerInvoices(Customer $customer): JsonResponse
    {
        $activeBranchId = BranchContext::getActiveBranchId();

        $query = Invoice::where('customer_id', $customer->id)
            ->whereIn('status', ['issued', 'partially_paid'])
            ->where('balance_amount', '>', 0)
            ->with(['jobCard.vehicle'])
            ->orderBy('invoice_date', 'asc')
            ->orderBy('id', 'asc');

        if ($activeBranchId) {
            $query->where(function ($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId)->orWhereNull('branch_id');
            });
        }

        $invoices = $query->get()->map(function ($inv) {
            return [
                'id' => $inv->id,
                'invoice_number' => $inv->invoice_number,
                'invoice_date' => $inv->invoice_date ? $inv->invoice_date->format('d M Y') : '—',
                'invoice_type' => ucfirst(str_replace('_', ' ', $inv->invoice_type ?? 'job_card')),
                'vehicle_number' => $inv->jobCard?->vehicle?->vehicle_number ?? '—',
                'grand_total' => (float) $inv->grand_total,
                'amount_paid' => (float) $inv->amount_paid,
                'balance_amount' => (float) $inv->balance_amount,
                'status' => $inv->status,
                'selected' => true, // Selected by default
                'allocated' => 0,
            ];
        });

        return response()->json([
            'success' => true,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->customer_name,
                'phone' => $customer->phone ?? 'N/A',
                'type' => ucfirst($customer->customer_type ?? 'individual'),
                'company_name' => $customer->company_name,
            ],
            'invoices' => $invoices,
            'total_outstanding' => (float) $invoices->sum('balance_amount'),
        ]);
    }

    /**
     * OPTION 1: Save split payment across multiple methods for a single invoice.
     */
    public function storeSplitPayment(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('record_payments')) {
            abort(403, 'Unauthorized to record payments.');
        }

        $validated = $request->validate([
            'invoice_id' => ['required', 'exists:invoices,id'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.payment_method' => ['required', 'in:cash,card,bank_transfer,cheque'],
            'payments.*.amount' => ['required', 'numeric', 'min:0.01'],
            'payments.*.reference_number' => ['nullable', 'string', 'max:100'],
            'payments.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($validated, $user) {
                $invoice = Invoice::where('id', $validated['invoice_id'])->lockForUpdate()->firstOrFail();

                if ($invoice->branch_id && ! $user->canAccessBranch($invoice->branch_id)) {
                    throw new Exception('Unauthorized to record payments for this invoice branch.');
                }

                if ($invoice->status === 'cancelled') {
                    throw new Exception('Cannot record payments against a cancelled invoice.');
                }

                $currentBalance = round((float) $invoice->balance_amount, 2);
                $totalPaying = 0.0;

                foreach ($validated['payments'] as $p) {
                    $amt = (float) $p['amount'];
                    $method = $p['payment_method'];
                    $ref = trim((string) ($p['reference_number'] ?? ''));

                    // Require reference for card, bank transfer, and cheque
                    if (in_array($method, ['card', 'bank_transfer', 'cheque']) && $ref === '') {
                        $methodName = ucfirst(str_replace('_', ' ', $method));
                        throw new Exception("Reference number / transaction ID is required for {$methodName} payments.");
                    }

                    $totalPaying += $amt;
                }

                if ($totalPaying <= 0) {
                    throw new Exception('Total payment amount must be greater than zero.');
                }

                // Prevent overpayment
                if (round($totalPaying, 2) > round($currentBalance, 2)) {
                    throw new Exception(
                        'Total paying (LKR '.number_format($totalPaying, 2).') exceeds the outstanding balance (LKR '.number_format($currentBalance, 2).').'
                    );
                }

                // Record each split payment
                foreach ($validated['payments'] as $p) {
                    $amt = round((float) $p['amount'], 2);
                    if ($amt <= 0) {
                        continue;
                    }

                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'job_card_id' => $invoice->job_card_id,
                        'customer_id' => $invoice->customer_id,
                        'payment_number' => Payment::generatePaymentNumber(),
                        'payment_date' => $validated['payment_date'],
                        'amount' => $amt,
                        'payment_method' => $p['payment_method'],
                        'reference_number' => $p['reference_number'] ?? null,
                        'notes' => $p['notes'] ?? 'Split payment entry',
                        'status' => 'completed',
                        'created_by' => $user->id,
                    ]);
                }

                // Update Invoice financial state
                $newPaid = round((float) $invoice->amount_paid + $totalPaying, 2);
                $grandTotal = round((float) $invoice->grand_total, 2);
                $newBalance = max(0, round($grandTotal - $newPaid, 2));
                $newStatus = ($newBalance <= 0.0001 || $newPaid >= $grandTotal) ? 'paid' : 'partially_paid';

                $invoice->update([
                    'amount_paid' => $newPaid,
                    'balance_amount' => $newBalance,
                    'status' => $newStatus,
                ]);

                // Audit log
                ActivityLog::log(
                    'split_payment',
                    'Recorded split payment of LKR '.number_format($totalPaying, 2)." for Invoice #{$invoice->invoice_number}. New balance: LKR ".number_format($newBalance, 2),
                    'Sales & Billing',
                    $user->id
                );
            });

            return redirect()->route('billing.multiple-invoice-payment.index', [
                'invoice_number' => Invoice::find($validated['invoice_id'])->invoice_number,
            ])->with('success', 'Split payment recorded successfully.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', "Payment failed: {$e->getMessage()}");
        }
    }

    /**
     * OPTION 2: Pay multiple invoices with one customer payment.
     */
    public function storeBulkPayment(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('record_payments')) {
            abort(403, 'Unauthorized to record payments.');
        }

        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'payment_method' => ['required', 'in:cash,card,bank_transfer,cheque'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'total_received' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:500'],
            'allocations' => ['required', 'array', 'min:1'],
            'allocations.*.invoice_id' => ['required', 'exists:invoices,id'],
            'allocations.*.allocated_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $activeBranchId = BranchContext::getActiveBranchId();

        try {
            DB::transaction(function () use ($validated, $user, $activeBranchId) {
                $customerId = $validated['customer_id'];
                $totalReceived = round((float) $validated['total_received'], 2);
                $allocations = $validated['allocations'];
                $method = $validated['payment_method'];
                $ref = trim((string) ($validated['reference_number'] ?? ''));

                if (in_array($method, ['card', 'bank_transfer', 'cheque']) && $ref === '') {
                    $methodName = ucfirst(str_replace('_', ' ', $method));
                    throw new Exception("Reference number / Cheque details are required for {$methodName} payments.");
                }

                // Filter out zero allocations
                $activeAllocations = array_filter($allocations, function ($alloc) {
                    return (float) ($alloc['allocated_amount'] ?? 0) > 0;
                });

                if (empty($activeAllocations)) {
                    throw new Exception('At least one invoice must have an allocated payment amount greater than zero.');
                }

                $totalAllocated = 0.0;
                foreach ($activeAllocations as $alloc) {
                    $totalAllocated += (float) $alloc['allocated_amount'];
                }
                $totalAllocated = round($totalAllocated, 2);

                if ($totalAllocated > $totalReceived) {
                    throw new Exception(
                        'Total allocated amount (LKR '.number_format($totalAllocated, 2).') exceeds total received amount (LKR '.number_format($totalReceived, 2).').'
                    );
                }

                // 1. Create Master Payment Receipt
                $receiptNumber = PaymentReceipt::generateReceiptNumber();
                $receipt = PaymentReceipt::create([
                    'receipt_number' => $receiptNumber,
                    'branch_id' => $activeBranchId,
                    'customer_id' => $customerId,
                    'receipt_date' => $validated['payment_date'],
                    'total_amount' => $totalReceived,
                    'payment_method' => $validated['payment_method'],
                    'reference_number' => $validated['reference_number'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'created_by' => $user->id,
                ]);

                // 2. Lock invoices and allocate amounts
                foreach ($activeAllocations as $alloc) {
                    $invId = $alloc['invoice_id'];
                    $allocAmt = round((float) $alloc['allocated_amount'], 2);

                    $invoice = Invoice::where('id', $invId)
                        ->where('customer_id', $customerId)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($invoice->branch_id && ! $user->canAccessBranch($invoice->branch_id)) {
                        throw new Exception("Unauthorized to allocate payments to Invoice #{$invoice->invoice_number}.");
                    }

                    if ($invoice->status === 'cancelled') {
                        throw new Exception("Invoice #{$invoice->invoice_number} is cancelled and cannot receive payments.");
                    }

                    $currentBalance = round((float) $invoice->balance_amount, 2);
                    if ($allocAmt > $currentBalance) {
                        throw new Exception(
                            'Allocation of LKR '.number_format($allocAmt, 2).' exceeds outstanding balance of LKR '.number_format($currentBalance, 2)." on Invoice #{$invoice->invoice_number}."
                        );
                    }

                    // Create Payment Allocation record
                    PaymentAllocation::create([
                        'payment_receipt_id' => $receipt->id,
                        'invoice_id' => $invoice->id,
                        'allocated_amount' => $allocAmt,
                        'notes' => "Allocated from Receipt #{$receiptNumber}",
                    ]);

                    // Create standard Payment record linked to receipt and invoice
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'job_card_id' => $invoice->job_card_id,
                        'customer_id' => $customerId,
                        'payment_receipt_id' => $receipt->id,
                        'payment_number' => Payment::generatePaymentNumber(),
                        'payment_date' => $validated['payment_date'],
                        'amount' => $allocAmt,
                        'payment_method' => $validated['payment_method'],
                        'reference_number' => $validated['reference_number'] ?? $receiptNumber,
                        'notes' => "Bulk Payment Receipt #{$receiptNumber}",
                        'status' => 'completed',
                        'created_by' => $user->id,
                    ]);

                    // Update invoice balances and status
                    $newPaid = round((float) $invoice->amount_paid + $allocAmt, 2);
                    $grandTotal = round((float) $invoice->grand_total, 2);
                    $newBalance = max(0, round($grandTotal - $newPaid, 2));
                    $newStatus = ($newBalance <= 0.0001 || $newPaid >= $grandTotal) ? 'paid' : 'partially_paid';

                    $invoice->update([
                        'amount_paid' => $newPaid,
                        'balance_amount' => $newBalance,
                        'status' => $newStatus,
                    ]);
                }

                // If receipt branch is null, set to first invoice's branch
                if (! $receipt->branch_id) {
                    $firstInv = Invoice::find($activeAllocations[array_key_first($activeAllocations)]['invoice_id']);
                    if ($firstInv && $firstInv->branch_id) {
                        $receipt->update(['branch_id' => $firstInv->branch_id]);
                    }
                }

                // 3. Activity Log Audit
                ActivityLog::log(
                    'multiple_invoice_payment',
                    "Processed Receipt #{$receiptNumber} for LKR ".number_format($totalReceived, 2).' allocated across '.count($activeAllocations).' invoices.',
                    'Sales & Billing',
                    $user->id
                );
            });

            return redirect()->route('billing.multiple-invoice-payment.index', ['tab' => 'bulk'])
                ->with('success', 'Multiple Invoice Payment receipt recorded successfully.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', "Payment allocation failed: {$e->getMessage()}");
        }
    }
}
