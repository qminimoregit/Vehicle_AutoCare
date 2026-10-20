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
        $rawSearch = (string) $request->query('search', '');
        $search = trim($rawSearch);
        $invoice = null;
        $error = null;
        $canCancel = true;
        $cancellationIneligibleReason = null;

        if ($search !== '') {
            $user = Auth::user();
            $normalized = ltrim($search, '#');
            $normalized = trim($normalized);
            $alphanumeric = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $normalized));

            $query = Invoice::with([
                'customer',
                'branch',
                'creator',
                'jobCard.vehicle.vehicleType',
                'jobCard.assignedTechnician',
                'jobCard.services.service',
                'jobCard.products.product',
                'linkedJobCard.vehicle.vehicleType',
                'linkedJobCard.assignedTechnician',
                'linkedJobCard.services.service',
                'linkedJobCard.products.product',
                'items',
                'payments.creator',
            ])
                ->where(function ($typeQ) {
                    $typeQ->whereIn('invoice_type', ['job_card', 'service'])
                        ->orWhereNotNull('job_card_id')
                        ->orWhereHas('linkedJobCard');
                })
                ->where(function ($notDirectQ) {
                    $notDirectQ->where('invoice_type', '!=', 'direct_sale')
                        ->orWhereNull('invoice_type');
                });

            // Search by Invoice Number, Job Number, or Vehicle Number
            $searchFilter = function ($q) use ($search, $normalized, $alphanumeric) {
                $q->where('invoice_number', $normalized)
                    ->orWhere('invoice_number', $search)
                    ->orWhere('invoice_number', 'like', "%{$normalized}%")
                    ->orWhere('reference', $normalized)
                    ->orWhere('reference', $search)
                    ->orWhereHas('jobCard', function ($jq) use ($search, $normalized, $alphanumeric) {
                        $jq->where('job_number', $normalized)
                            ->orWhere('job_number', $search)
                            ->orWhereHas('vehicle', function ($vq) use ($normalized, $alphanumeric) {
                                $vq->where('vehicle_number', 'like', "%{$normalized}%")
                                    ->when($alphanumeric !== '', function ($aq) use ($alphanumeric) {
                                        $aq->orWhere('vehicle_number_normalized', 'like', "%{$alphanumeric}%");
                                    });
                            });
                    })
                    ->orWhereHas('linkedJobCard', function ($jq) use ($search, $normalized, $alphanumeric) {
                        $jq->where('job_number', $normalized)
                            ->orWhere('job_number', $search)
                            ->orWhereHas('vehicle', function ($vq) use ($normalized, $alphanumeric) {
                                $vq->where('vehicle_number', 'like', "%{$normalized}%")
                                    ->when($alphanumeric !== '', function ($aq) use ($alphanumeric) {
                                        $aq->orWhere('vehicle_number_normalized', 'like', "%{$alphanumeric}%");
                                    });
                            });
                    })
                    ->orWhere('customer_snapshot->vehicle_number', 'like', "%{$normalized}%");
            };

            $candidate = $query->where($searchFilter)->first();

            if (! $candidate) {
                // Check if it's a Direct Sale invoice to provide actionable feedback
                $directSaleInvoice = Invoice::where('invoice_type', 'direct_sale')
                    ->where(function ($dq) use ($search, $normalized) {
                        $dq->where('invoice_number', $normalized)
                            ->orWhere('invoice_number', $search);
                    })->first();

                if ($directSaleInvoice) {
                    $error = "Invoice #{$directSaleInvoice->invoice_number} is a Direct Sale invoice. Please use Avoid Direct Sale to manage point-of-sale transactions.";
                } else {
                    $error = 'No matching service invoice found.';
                }
            } else {
                // Branch authorization check
                if ($user && $candidate->branch_id && ! $user->canAccessBranch($candidate->branch_id)) {
                    abort(403, 'Unauthorized access to this branch.');
                }

                // Check active branch context for non-admin users
                $activeBranchId = BranchContext::getActiveBranchId();
                if ($user && ! $user->isAdmin() && $activeBranchId && $candidate->branch_id && $candidate->branch_id != $activeBranchId) {
                    abort(403, 'Unauthorized. This invoice belongs to a different branch than your current active branch.');
                }

                $invoice = $candidate;

                // Ensure jobCard relation is set if linked via invoice_id
                if (! $invoice->jobCard && $invoice->linkedJobCard) {
                    $invoice->setRelation('jobCard', $invoice->linkedJobCard);
                }

                // Determine cancellation eligibility separately from lookup
                if ($invoice->status === 'cancelled') {
                    $canCancel = false;
                    $cancellationIneligibleReason = 'This transaction has already been cancelled.';
                } elseif (! $user || ! $user->isAdmin()) {
                    $canCancel = false;
                    $cancellationIneligibleReason = 'Only system administrators are authorized to avoid service sales.';
                }
            }
        }

        return view('billing.avoid-service-sale.index', compact('invoice', 'search', 'error', 'canCancel', 'cancellationIneligibleReason'));
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

        // Branch authorization check
        if ($invoice->branch_id && ! $user->canAccessBranch($invoice->branch_id)) {
            abort(403, 'Unauthorized access to this branch.');
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

                $jobCard = $freshInvoice->job_card_id
                    ? JobCard::find($freshInvoice->job_card_id)
                    : JobCard::where('invoice_id', $freshInvoice->id)->first();
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
