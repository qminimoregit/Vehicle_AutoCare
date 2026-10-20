<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Display the specified invoice with line items, payments, and actions.
     */
    public function show(Invoice $invoice): View
    {
        $invoice->load([
            'customer',
            'jobCard.vehicle',
            'jobCard.vehicleType',
            'items',
            'payments.creator',
            'creator',
        ]);

        $company = CompanySetting::getSettings();

        return view('operations.invoices.show', compact('invoice', 'company'));
    }

    /**
     * Printable view of the invoice / receipt.
     */
    public function print(Invoice $invoice): View
    {
        $invoice->load([
            'customer',
            'jobCard.vehicle',
            'jobCard.vehicleType',
            'items',
            'payments.creator',
            'creator',
        ]);

        $company = CompanySetting::getSettings();

        return view('operations.invoices.print', compact('invoice', 'company'));
    }

    /**
     * Record a payment against an outstanding invoice.
     */
    public function addPayment(Request $request, Invoice $invoice): RedirectResponse
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Invoice is already fully paid.');
        }

        if ($invoice->status === 'cancelled') {
            return back()->with('error', 'Cannot record payment for a cancelled invoice.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', "max:{$invoice->balance_amount}"],
            'payment_method' => ['required', 'in:cash,card,bank_transfer,cheque'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($invoice, $validated) {
            $paymentAmount = (float) $validated['amount'];

            Payment::create([
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id,
                'payment_number' => Payment::generatePaymentNumber(),
                'payment_date' => now()->toDateString(),
                'amount' => $paymentAmount,
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $newPaid = (float) $invoice->amount_paid + $paymentAmount;
            $newBalance = max(0, (float) $invoice->grand_total - $newPaid);
            $newStatus = $newBalance <= 0 ? 'paid' : 'partially_paid';

            $invoice->update([
                'amount_paid' => $newPaid,
                'balance_amount' => $newBalance,
                'status' => $newStatus,
            ]);
        });

        return back()->with('success', 'Payment recorded successfully.');
    }
}
