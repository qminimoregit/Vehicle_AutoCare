<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\JobCard;
use App\Services\BranchContext;
use App\Services\Inventory\StockService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AvoidServiceSaleController extends Controller
{
    public function __construct(
        protected StockService $stockService
    ) {}

    /**
     * Display the Avoid Service Sale search and cancellation screen.
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
                'jobCard.vehicle.vehicleType',
                'jobCard.assignedTechnician',
                'jobCard.services.service',
                'jobCard.products.product',
                'items',
                'payments.creator',
            ])->where('invoice_type', 'job_card');

            // Branch scoping
            if ($activeBranchId) {
                $query->where(function ($q) use ($activeBranchId) {
                    $q->where('branch_id', $activeBranchId)
                        ->orWhereNull('branch_id');
                });
            }

            // Search by Invoice Number, Job Number, or Vehicle Number
            $invoice = $query->where(function ($q) use ($search) {
                $q->where('invoice_number', $search)
                    ->orWhere('reference', $search)
                    ->orWhereHas('jobCard', function ($jq) use ($search) {
                        $jq->where('job_number', $search)
                            ->orWhereHas('vehicle', function ($vq) use ($search) {
                                $vq->where('vehicle_number', 'like', "%{$search}%")
                                    ->orWhere('vehicle_number_normalized', 'like', '%'.strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $search)).'%');
                            });
                    });
            })->first();

            if (! $invoice) {
                $error = "No Service Invoice found matching '{$search}'. Please check the Invoice, Job, or Vehicle number.";
            }
        }

        return view('billing.avoid-service-sale.index', compact('invoice', 'search', 'error'));
    }

    /**
     * Process cancellation of an incorrect Service Sale / Service Invoice.
     */
    public function cancel(Request $request, Invoice $invoice): RedirectResponse
    {
        $user = Auth::user();

        // 1. Validate Admin permission
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Unauthorized. Only system administrators can avoid service sales.');
        }

        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'min:3', 'max:500'],
            'confirm_avoid' => ['required', 'accepted'],
            'restore_parts_stock' => ['nullable', 'boolean'],
        ]);

        $reason = trim($validated['cancellation_reason']);
        $restorePartsStock = $request->boolean('restore_parts_stock', false);

        try {
            DB::transaction(function () use ($invoice, $user, $reason, $restorePartsStock) {
                // Lock invoice row
                $freshInvoice = Invoice::where('id', $invoice->id)->lockForUpdate()->firstOrFail();

                // Prevent duplicate cancellation
                if ($freshInvoice->status === 'cancelled') {
                    throw new Exception("Service Invoice #{$freshInvoice->invoice_number} is already cancelled.");
                }

                $jobCard = $freshInvoice->job_card_id ? JobCard::find($freshInvoice->job_card_id) : null;
                $stockRestored = false;

                // If parts stock return is specifically authorized by Admin
                if ($restorePartsStock && $jobCard) {
                    foreach ($jobCard->products as $productItem) {
                        if ($productItem->is_stock_deducted && ! empty($productItem->batch_allocations)) {
                            $this->stockService->reverseStockFifo(
                                $productItem->batch_allocations,
                                $jobCard,
                                $user,
                                "Avoid Service Sale #{$freshInvoice->invoice_number}: {$reason}"
                            );
                            $productItem->update(['is_stock_deducted' => false]);
                            $stockRestored = true;
                        }
                    }
                }

                // 2. Mark Invoice cancelled and reverse financial balance
                $freshInvoice->update([
                    'status' => 'cancelled',
                    'amount_paid' => 0.00,
                    'balance_amount' => 0.00,
                    'cancellation_reason' => $reason,
                    'cancelled_by' => $user->id,
                    'cancelled_at' => now(),
                    'notes' => trim(($freshInvoice->notes ? $freshInvoice->notes."\n" : '')."Cancelled via Avoid Service Sale: {$reason}"),
                ]);

                // 3. Reverse any payments associated with this invoice
                foreach ($freshInvoice->payments as $payment) {
                    $payment->update([
                        'status' => 'reversed',
                        'reversed_at' => now(),
                        'reversal_reason' => "Avoid Service Sale #{$freshInvoice->invoice_number}: {$reason}",
                    ]);
                }

                // 4. Job Card and Vehicle preservation:
                // Do NOT delete the vehicle or job card. Preserve vehicle service history.
                if ($jobCard) {
                    $jobCard->update([
                        'service_notes' => trim(($jobCard->service_notes ? $jobCard->service_notes."\n" : '').'Billing cancelled on '.now()->format('Y-m-d').": {$reason}"),
                    ]);
                }

                // 5. Activity Log Audit
                $jobNum = $jobCard ? " (Job #{$jobCard->job_number})" : '';
                $vehNum = $jobCard && $jobCard->vehicle ? " for vehicle {$jobCard->vehicle->vehicle_number}" : '';
                ActivityLog::log(
                    'avoid_service_sale',
                    "Cancelled Service Invoice #{$freshInvoice->invoice_number}{$jobNum}{$vehNum}. Reason: {$reason}".($stockRestored ? ' (Parts returned to FIFO batches)' : ' (Parts consumed/not returned)'),
                    'Sales & Billing',
                    $user->id
                );
            });

            return redirect()->route('billing.avoid-service-sale.index', ['search' => $invoice->invoice_number])
                ->with('success', "Service Invoice #{$invoice->invoice_number} has been cancelled successfully. Service history and vehicle records remain intact.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', "Cancellation failed: {$e->getMessage()}");
        }
    }
}
