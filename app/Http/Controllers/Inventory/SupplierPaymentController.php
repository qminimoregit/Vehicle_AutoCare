<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreSupplierPaymentRequest;
use App\Models\ActivityLog;
use App\Models\CompanySetting;
use App\Models\GoodsReceivedNote;
use App\Models\SupplierPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SupplierPaymentController extends Controller
{
    /**
     * Record a payment against a Supplier Invoice / GRN.
     */
    public function store(StoreSupplierPaymentRequest $request, GoodsReceivedNote $grn): RedirectResponse
    {
        $user = $request->user();

        // Branch access check
        if ($grn->branch_id && ! $user->canAccessBranch($grn->branch_id)) {
            abort(403, 'Unauthorized access to this branch.');
        }

        $amount = (float) $request->input('amount');

        return DB::transaction(function () use ($grn, $request, $user, $amount) {
            // Lock GRN record to prevent concurrent overpayments
            $freshGrn = GoodsReceivedNote::where('id', $grn->id)->lockForUpdate()->firstOrFail();
            $outstanding = $freshGrn->outstanding_amount;

            if ($outstanding <= 0.0001) {
                return back()->with('error', "GRN #{$freshGrn->grn_number} is already fully paid.");
            }

            if ($amount > $outstanding) {
                return back()->withInput()->with(
                    'error',
                    'Payment amount (LKR '.number_format($amount, 2).') cannot exceed the outstanding balance of LKR '.number_format($outstanding, 2).'.'
                );
            }

            $paymentNumber = SupplierPayment::generatePaymentNumber();

            $payment = SupplierPayment::create([
                'payment_number' => $paymentNumber,
                'supplier_id' => $freshGrn->supplier_id,
                'branch_id' => $freshGrn->branch_id,
                'grn_id' => $freshGrn->id,
                'payment_date' => $request->input('payment_date'),
                'amount' => $amount,
                'payment_method' => $request->input('payment_method'),
                'reference_number' => $request->input('reference_number'),
                'notes' => $request->input('notes'),
                'status' => 'completed',
                'created_by' => $user->id,
            ]);

            ActivityLog::log(
                action: 'payment_recorded',
                description: "Recorded supplier payment {$paymentNumber} of LKR ".number_format($amount, 2)." for GRN #{$freshGrn->grn_number} ({$freshGrn->supplier->name}) via ".ucfirst(str_replace('_', ' ', $payment->payment_method)).'.',
                module: 'Purchasing (GRN)',
                userId: $user->id
            );

            $remaining = $freshGrn->fresh()->outstanding_amount;
            $msg = "Payment {$paymentNumber} of LKR ".number_format($amount, 2).' recorded successfully. Remaining balance: LKR '.number_format($remaining, 2).'.';

            return back()->with('success', $msg);
        });
    }

    /**
     * Safely reverse an incorrect supplier payment (Admin only).
     */
    public function reverse(Request $request, SupplierPayment $payment): RedirectResponse
    {
        $user = $request->user();

        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Unauthorized. Only administrators may reverse supplier payments.');
        }

        $request->validate([
            'reversal_reason' => ['required', 'string', 'max:500'],
        ]);

        if ($payment->isReversed()) {
            return back()->with('error', 'This payment has already been reversed.');
        }

        DB::transaction(function () use ($payment, $request, $user) {
            $payment->update([
                'status' => 'reversed',
                'reversed_at' => now(),
                'reversed_by' => $user->id,
                'reversal_reason' => $request->input('reversal_reason'),
            ]);

            ActivityLog::log(
                action: 'payment_reversed',
                description: "Reversed supplier payment {$payment->payment_number} of LKR ".number_format($payment->amount, 2)." for GRN #{$payment->grn->grn_number}. Reason: {$request->input('reversal_reason')}",
                module: 'Purchasing (GRN)',
                userId: $user->id
            );
        });

        return back()->with('success', "Payment {$payment->payment_number} has been reversed successfully.");
    }

    /**
     * Backward-compatible destroy endpoint (redirects to reverse workflow).
     */
    public function destroy(Request $request, SupplierPayment $payment): RedirectResponse
    {
        return $this->reverse($request, $payment);
    }

    /**
     * View and print a supplier payment receipt / voucher.
     */
    public function receipt(Request $request, SupplierPayment $payment): View
    {
        $user = $request->user();
        if (! $user || (! $user->isAdmin() && ! $user->hasPermission('view_invoices') && ! $user->hasPermission('record_payments'))) {
            abort(403, 'Unauthorized access to payment receipt.');
        }

        $payment->load(['supplier', 'grn', 'createdBy', 'branch', 'reversedBy']);
        $company = CompanySetting::first();

        return view('inventory.grn.payment-receipt', compact('payment', 'company'));
    }
}
