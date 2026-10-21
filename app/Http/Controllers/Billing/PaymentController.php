<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Mail\PaymentReceiptMail;
use App\Models\ActivityLog;
use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\BranchContext;
use App\Services\NumberToWordsHelper;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Display the dedicated Payments page under Sales & Billing.
     * Features: Outstanding invoices, payment recording modal, status filtering, and recent receipts.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('view_invoices') && ! $user->hasPermission('record_payments')) {
            abort(403, 'Unauthorized access to Payments.');
        }

        $status = $request->query('status', 'all');
        $search = trim((string) $request->query('search', ''));
        $activeBranchId = BranchContext::getActiveBranchId();

        $query = Invoice::with(['customer', 'jobCard.vehicle', 'quotation', 'branch', 'creator', 'payments'])
            ->where('status', '!=', 'cancelled')
            ->orderBy('id', 'desc');

        // Scoping by Branch if user has an active branch
        if ($activeBranchId) {
            $query->where(function ($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId)
                    ->orWhereNull('branch_id');
            });
        }

        // Status Filter
        if ($status === 'unpaid') {
            $query->where(function ($q) {
                $q->where('status', 'issued')
                    ->orWhere('amount_paid', '<=', 0.0001);
            })->where('balance_amount', '>', 0);
        } elseif ($status === 'partially_paid') {
            $query->where('status', 'partially_paid')
                ->where('balance_amount', '>', 0);
        } elseif ($status === 'paid') {
            $query->where('status', 'paid');
        }

        // Search Filter
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('customer_name', 'like', "%{$search}%")
                            ->orWhere('company_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('jobCard.vehicle', function ($vq) use ($search) {
                        $vq->where('vehicle_number', 'like', "%{$search}%");
                    });
            });
        }

        $invoices = $query->paginate(15)->withQueryString();

        // Base query for counts and stats
        $baseQuery = Invoice::query()->where('status', '!=', 'cancelled');
        if ($activeBranchId) {
            $baseQuery->where(function ($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId)
                    ->orWhereNull('branch_id');
            });
        }

        $counts = [
            'all' => (clone $baseQuery)->count(),
            'unpaid' => (clone $baseQuery)->where(function ($q) {
                $q->where('status', 'issued')->orWhere('amount_paid', '<=', 0.0001);
            })->where('balance_amount', '>', 0)->count(),
            'partially_paid' => (clone $baseQuery)->where('status', 'partially_paid')->where('balance_amount', '>', 0)->count(),
            'paid' => (clone $baseQuery)->where('status', 'paid')->count(),
        ];

        $totalOutstanding = (clone $baseQuery)->sum('balance_amount');
        $totalCollected = (clone $baseQuery)->sum('amount_paid');

        // Recent completed payment records for quick lookup and receipts
        $recentPaymentsQuery = Payment::with(['invoice.customer', 'invoice.jobCard.vehicle', 'creator'])
            ->where('status', 'completed')
            ->orderBy('id', 'desc');

        if ($activeBranchId) {
            $recentPaymentsQuery->whereHas('invoice', function ($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId)->orWhereNull('branch_id');
            });
        }

        $recentPayments = $recentPaymentsQuery->limit(10)->get();

        return view('billing.payments.index', compact(
            'invoices',
            'recentPayments',
            'status',
            'search',
            'counts',
            'totalOutstanding',
            'totalCollected'
        ));
    }

    /**
     * Record a single or partial payment against an invoice.
     * Enforces database transactions, row locking, balance recalculation, and stock isolation.
     */
    public function store(Request $request, Invoice $invoice): RedirectResponse
    {
        $user = $request->user();

        // Authorization check
        if (! $user->isAdmin() && ! $user->hasPermission('record_payments')) {
            abort(403, 'Unauthorized to record payments.');
        }

        // Branch access check
        if ($invoice->branch_id && ! $user->canAccessBranch($invoice->branch_id)) {
            abort(403, 'Unauthorized access to this branch.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,card,bank_transfer,cheque'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $payment = DB::transaction(function () use ($invoice, $validated, $user) {
                // Lock invoice row for update to prevent concurrent race conditions
                $freshInvoice = Invoice::where('id', $invoice->id)->lockForUpdate()->firstOrFail();

                if ($freshInvoice->status === 'cancelled') {
                    throw new Exception("Invoice #{$freshInvoice->invoice_number} is cancelled and cannot accept payments.");
                }

                $currentBalance = round((float) $freshInvoice->balance_amount, 2);
                $payAmount = round((float) $validated['amount'], 2);

                if ($currentBalance <= 0.0001) {
                    throw new Exception("Invoice #{$freshInvoice->invoice_number} is already paid in full.");
                }

                // Prevent overpayment
                if ($payAmount > $currentBalance) {
                    throw new Exception(
                        'Payment amount (LKR '.number_format($payAmount, 2).') cannot exceed the outstanding balance of LKR '.number_format($currentBalance, 2).'.'
                    );
                }

                $paymentNumber = Payment::generatePaymentNumber();

                $payment = Payment::create([
                    'invoice_id' => $freshInvoice->id,
                    'customer_id' => $freshInvoice->customer_id,
                    'payment_number' => $paymentNumber,
                    'payment_date' => $validated['payment_date'],
                    'amount' => $payAmount,
                    'payment_method' => $validated['payment_method'],
                    'reference_number' => $validated['reference_number'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'completed',
                    'created_by' => $user->id,
                ]);

                // Recalculate invoice financial state from valid completed payments
                $totalPaid = (float) Payment::where('invoice_id', $freshInvoice->id)
                    ->where('status', 'completed')
                    ->sum('amount');

                $totalPaid = round($totalPaid, 2);
                $grandTotal = round((float) $freshInvoice->grand_total, 2);
                $newBalance = max(0, round($grandTotal - $totalPaid, 2));

                $newStatus = ($newBalance <= 0.0001 || $totalPaid >= $grandTotal)
                    ? 'paid'
                    : ($totalPaid > 0 ? 'partially_paid' : 'issued');

                $freshInvoice->update([
                    'amount_paid' => $totalPaid,
                    'balance_amount' => $newBalance,
                    'status' => $newStatus,
                ]);

                // Audit Log
                ActivityLog::log(
                    action: 'payment_recorded',
                    description: "Recorded payment {$paymentNumber} of LKR ".number_format($payAmount, 2)." for Invoice #{$freshInvoice->invoice_number}. New balance: LKR ".number_format($newBalance, 2).'.',
                    module: 'Sales & Billing',
                    userId: $user->id
                );

                return $payment;
            });

            return back()->with('success', "Payment {$payment->payment_number} of LKR ".number_format($payment->amount, 2).' recorded successfully.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', "Payment failed: {$e->getMessage()}");
        }
    }

    /**
     * Display printable official Payment Receipt for customer.
     */
    public function receipt(Request $request, Payment $payment): View
    {
        $user = $request->user();
        if (! $user || (! $user->isAdmin() && ! $user->hasPermission('view_invoices') && ! $user->hasPermission('record_payments'))) {
            abort(403, 'Unauthorized access to payment receipt.');
        }

        $payment->load([
            'invoice.customer',
            'invoice.jobCard.vehicle',
            'invoice.jobCard.vehicleType',
            'invoice.branch',
            'creator',
            'customer',
        ]);

        if ($payment->invoice && $payment->invoice->branch_id && ! $user->canAccessBranch($payment->invoice->branch_id)) {
            abort(403, 'Unauthorized access to this branch.');
        }

        $company = CompanySetting::getSettings();
        $amountInWords = NumberToWordsHelper::spell($payment->amount, 'LKR');

        return view('billing.payments.receipt', compact('payment', 'company', 'amountInWords'));
    }

    /**
     * Display printable 80mm compact thermal Payment Receipt.
     */
    public function thermalReceipt(Request $request, Payment $payment): View
    {
        $user = $request->user();
        if (! $user || (! $user->isAdmin() && ! $user->hasPermission('view_invoices') && ! $user->hasPermission('record_payments'))) {
            abort(403, 'Unauthorized access to payment receipt.');
        }

        $payment->load([
            'invoice.customer',
            'invoice.jobCard.vehicle',
            'invoice.jobCard.vehicleType',
            'invoice.branch',
            'creator',
            'customer',
        ]);

        if ($payment->invoice && $payment->invoice->branch_id && ! $user->canAccessBranch($payment->invoice->branch_id)) {
            abort(403, 'Unauthorized access to this branch.');
        }

        $company = CompanySetting::getSettings();

        return view('billing.payments.thermal', compact('payment', 'company'));
    }

    /**
     * Send payment receipt PDF via email to customer.
     */
    public function sendEmail(Request $request, Payment $payment): RedirectResponse
    {
        $user = $request->user();
        if (! $user || (! $user->isAdmin() && ! $user->hasPermission('record_payments'))) {
            abort(403, 'Unauthorized to email receipts.');
        }

        $email = $payment->customer?->email ?? ($payment->invoice?->customer?->email ?? null);

        if (empty($email)) {
            return back()->with('warning', 'No email address registered for this customer.');
        }

        $payment->load([
            'invoice.customer',
            'invoice.jobCard.vehicle',
            'invoice.branch',
            'creator',
            'customer',
        ]);

        $company = CompanySetting::getSettings();

        try {
            Mail::to($email)->send(new PaymentReceiptMail($payment, $company));

            ActivityLog::log(
                action: 'payment_receipt_emailed',
                description: "Emailed Payment Receipt {$payment->payment_number} to {$email}.",
                module: 'Sales & Billing',
                userId: $user->id
            );

            return back()->with('success', "Payment receipt emailed successfully to {$email}.");
        } catch (Exception $e) {
            ActivityLog::log(
                action: 'payment_receipt_email_failed',
                description: "Failed to email Payment Receipt {$payment->payment_number} to {$email}: {$e->getMessage()}",
                module: 'Sales & Billing',
                userId: $user->id
            );

            return back()->with('error', "Failed to send email: {$e->getMessage()}");
        }
    }

    /**
     * Reverse an incorrect payment (Admin only).
     */
    public function reverse(Request $request, Payment $payment): RedirectResponse
    {
        $user = $request->user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Unauthorized. Only administrators may reverse payments.');
        }

        $request->validate([
            'reversal_reason' => ['required', 'string', 'max:500'],
        ]);

        if ($payment->status === 'reversed') {
            return back()->with('error', 'This payment has already been reversed.');
        }

        try {
            DB::transaction(function () use ($payment, $request, $user) {
                $payment->update([
                    'status' => 'reversed',
                    'reversed_at' => now(),
                    'reversal_reason' => $request->input('reversal_reason'),
                ]);

                $invoice = Invoice::where('id', $payment->invoice_id)->lockForUpdate()->firstOrFail();

                // Recalculate without reversed payment
                $totalPaid = (float) Payment::where('invoice_id', $invoice->id)
                    ->where('status', 'completed')
                    ->sum('amount');

                $totalPaid = round($totalPaid, 2);
                $grandTotal = round((float) $invoice->grand_total, 2);
                $newBalance = max(0, round($grandTotal - $totalPaid, 2));

                $newStatus = ($newBalance <= 0.0001 || $totalPaid >= $grandTotal)
                    ? 'paid'
                    : ($totalPaid > 0 ? 'partially_paid' : 'issued');

                $invoice->update([
                    'amount_paid' => $totalPaid,
                    'balance_amount' => $newBalance,
                    'status' => $newStatus,
                ]);

                ActivityLog::log(
                    action: 'payment_reversed',
                    description: "Reversed payment {$payment->payment_number} of LKR ".number_format($payment->amount, 2)." for Invoice #{$invoice->invoice_number}. Reason: {$request->input('reversal_reason')}",
                    module: 'Sales & Billing',
                    userId: $user->id
                );
            });

            return back()->with('success', "Payment {$payment->payment_number} has been reversed successfully.");
        } catch (Exception $e) {
            return back()->with('error', "Payment reversal failed: {$e->getMessage()}");
        }
    }

    /**
     * Display compact 80mm thermal payment receipt.
     */
    public function thermalReceipt(Request $request, Payment $payment): View
    {
        $user = $request->user();
        if (! $user || (! $user->isAdmin() && ! $user->hasPermission('view_invoices') && ! $user->hasPermission('record_payments'))) {
            abort(403, 'Unauthorized access to payment receipt.');
        }

        $payment->load([
            'invoice.customer',
            'invoice.jobCard.vehicle',
            'invoice.jobCard.vehicleType',
            'invoice.branch',
            'creator',
            'customer',
        ]);

        if ($payment->invoice && $payment->invoice->branch_id && ! $user->canAccessBranch($payment->invoice->branch_id)) {
            abort(403, 'Unauthorized access to this branch.');
        }

        $company = CompanySetting::getSettings();

        return view('billing.payments.thermal', compact('payment', 'company'));
    }

    /**
     * Send payment receipt PDF via email to customer.
     */
    public function sendEmail(Request $request, Payment $payment): RedirectResponse
    {
        $payment->load(['customer', 'invoice.customer']);
        $customer = $payment->customer ?? $payment->invoice?->customer;

        if (! $customer || empty($customer->email)) {
            return back()->with('error', 'No email address registered for this customer.');
        }

        try {
            Mail::to($customer->email)->send(new PaymentReceiptMail($payment));

            ActivityLog::log(
                action: 'payment_receipt_emailed',
                description: "Emailed Payment Receipt #{$payment->payment_number} to {$customer->email}.",
                module: 'Sales & Billing'
            );

            return back()->with('success', "Payment receipt emailed successfully to {$customer->email}.");
        } catch (\Throwable $e) {
            ActivityLog::log(
                action: 'payment_receipt_email_failed',
                description: "Failed emailing payment receipt #{$payment->payment_number} to {$customer->email}: {$e->getMessage()}",
                module: 'Sales & Billing'
            );

            return back()->with('error', "Could not send email: {$e->getMessage()}");
        }
    }
}

