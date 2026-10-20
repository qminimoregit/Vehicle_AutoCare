<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\PayrollPeriod;
use App\Services\BranchContext;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceDateChangeController extends Controller
{
    /**
     * Display the Invoice Date Change search and editing screen.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $invoice = null;
        $error = null;

        if ($search !== '') {
            $activeBranchId = BranchContext::getActiveBranchId();

            $query = Invoice::with([
                'customer',
                'branch',
                'creator',
                'dateChangedBy',
                'jobCard.vehicle',
            ]);

            // Branch scoping
            if ($activeBranchId) {
                $query->where(function ($q) use ($activeBranchId) {
                    $q->where('branch_id', $activeBranchId)
                        ->orWhereNull('branch_id');
                });
            }

            $invoice = $query->where('invoice_number', $search)->first();

            if (! $invoice) {
                $error = "No Invoice found with number '{$search}'.";
            }
        }

        return view('billing.invoice-date-change.index', compact('invoice', 'search', 'error'));
    }

    /**
     * Update an authorized invoice date.
     */
    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $user = Auth::user();

        // 1. Admin only
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Unauthorized. Only administrators can alter invoice dates.');
        }

        $validated = $request->validate([
            'new_invoice_date' => ['required', 'date', 'before_or_equal:today'],
            'reason' => ['required', 'string', 'min:3', 'max:500'],
        ]);

        $newDate = $validated['new_invoice_date'];
        $reason = trim($validated['reason']);

        // Check if invoice is cancelled
        if ($invoice->status === 'cancelled') {
            return back()->with('error', 'Cannot change invoice date on a cancelled or voided invoice.');
        }

        // Check locked accounting / payroll period restrictions
        $isPeriodLocked = PayrollPeriod::where('status', 'approved')
            ->where('start_date', '<=', $newDate)
            ->where('end_date', '>=', $newDate)
            ->exists();

        if ($isPeriodLocked) {
            return back()->with('error', "The selected date ({$newDate}) falls within a closed and locked accounting period. Date modification not permitted.");
        }

        try {
            DB::transaction(function () use ($invoice, $user, $newDate, $reason) {
                $freshInvoice = Invoice::where('id', $invoice->id)->lockForUpdate()->firstOrFail();

                $oldDate = $freshInvoice->invoice_date->toDateString();

                // If original invoice date has never been recorded, preserve it now
                $originalDate = $freshInvoice->original_invoice_date ?? $freshInvoice->invoice_date;

                $freshInvoice->update([
                    'original_invoice_date' => $originalDate,
                    'invoice_date' => $newDate,
                    'date_changed_by' => $user->id,
                    'date_changed_at' => now(),
                    'date_change_reason' => $reason,
                ]);

                // Record Activity Log
                ActivityLog::log(
                    'invoice_date_change',
                    "Changed date on Invoice #{$freshInvoice->invoice_number} from {$oldDate} to {$newDate}. Reason: {$reason}",
                    'Sales & Billing',
                    $user->id
                );
            });

            return redirect()->route('billing.invoice-date-change.index', ['search' => $invoice->invoice_number])
                ->with('success', "Invoice #{$invoice->invoice_number} date updated from {$invoice->invoice_date->format('d M Y')} to ".date('d M Y', strtotime($newDate)).'.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', "Date change failed: {$e->getMessage()}");
        }
    }
}
