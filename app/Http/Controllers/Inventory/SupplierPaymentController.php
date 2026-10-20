<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreSupplierPaymentRequest;
use App\Models\ActivityLog;
use App\Models\GoodsReceivedNote;
use App\Models\SupplierPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupplierPaymentController extends Controller
{
    /**
     * Record a payment against a Supplier Invoice / GRN.
     */
    public function store(StoreSupplierPaymentRequest $request, GoodsReceivedNote $grn): RedirectResponse
    {
        $amount = (float) $request->input('amount');
        $outstanding = $grn->outstanding_amount;

        if ($amount > $outstanding && $outstanding > 0) {
            // Cap or allow with notice, but prevent severe overpayments
            if ($amount > $outstanding * 1.5) {
                return back()->withInput()->with('error', "Payment amount (LKR {$amount}) exceeds the remaining balance (LKR {$outstanding}) by an unreasonable margin.");
            }
        }

        $payment = SupplierPayment::create([
            'supplier_id' => $grn->supplier_id,
            'grn_id' => $grn->id,
            'payment_date' => $request->input('payment_date'),
            'amount' => $amount,
            'payment_method' => $request->input('payment_method'),
            'reference_number' => $request->input('reference_number'),
            'notes' => $request->input('notes'),
            'created_by' => $request->user()->id,
        ]);

        ActivityLog::log(
            action: 'payment_recorded',
            description: "Recorded payment of LKR {$amount} for GRN #{$grn->grn_number} ({$grn->supplier->name}).",
            module: 'Purchasing (GRN)'
        );

        return back()->with('success', 'Payment of LKR '.number_format($amount, 2).' recorded successfully.');
    }

    /**
     * Delete / void an incorrect supplier payment.
     */
    public function destroy(Request $request, SupplierPayment $payment): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $grn = $payment->grn;
        $amount = $payment->amount;
        $payment->delete();

        ActivityLog::log(
            action: 'payment_voided',
            description: "Voided payment of LKR {$amount} for GRN #{$grn->grn_number}.",
            module: 'Purchasing (GRN)'
        );

        return back()->with('success', 'Payment of LKR '.number_format($amount, 2).' has been voided.');
    }
}
