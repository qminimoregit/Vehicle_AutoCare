<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DirectSale;
use App\Models\Invoice;
use App\Services\BranchContext;
use App\Services\Inventory\StockService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AvoidDirectSaleController extends Controller
{
    public function __construct(
        protected StockService $stockService
    ) {}

    /**
     * Display the Avoid Direct Sale search and cancellation screen.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $directSale = null;
        $error = null;

        if ($search !== '') {
            $activeBranchId = BranchContext::getActiveBranchId();

            $query = DirectSale::with([
                'customer',
                'branch',
                'creator',
                'items.product.uom',
                'invoice.payments.creator',
            ]);

            // Branch scoping
            if ($activeBranchId) {
                $query->where(function ($q) use ($activeBranchId) {
                    $q->where('branch_id', $activeBranchId)
                        ->orWhereNull('branch_id');
                });
            }

            // Search by invoice number or sale number
            $directSale = $query->where(function ($q) use ($search) {
                $q->where('sale_number', $search)
                    ->orWhereHas('invoice', function ($iq) use ($search) {
                        $iq->where('invoice_number', $search);
                    });
            })->first();

            if (! $directSale) {
                $error = "No Direct Sale found matching '{$search}'. Please check the Invoice or Sale number.";
            }
        }

        return view('billing.avoid-direct-sale.index', compact('directSale', 'search', 'error'));
    }

    /**
     * Process cancellation of an incorrect Direct Sale transaction.
     */
    public function cancel(Request $request, DirectSale $directSale): RedirectResponse
    {
        $user = Auth::user();

        // 1. Validate Admin permission
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Unauthorized. Only system administrators can avoid direct sales.');
        }

        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'min:3', 'max:500'],
            'confirm_avoid' => ['required', 'accepted'],
            'restore_stock' => ['nullable', 'boolean'],
        ]);

        $reason = trim($validated['cancellation_reason']);
        $restoreStock = $request->boolean('restore_stock', true);

        try {
            DB::transaction(function () use ($directSale, $user, $reason, $restoreStock) {
                // Lock direct sale row
                $freshSale = DirectSale::where('id', $directSale->id)->lockForUpdate()->firstOrFail();

                // Prevent duplicate cancellation
                if ($freshSale->status === 'cancelled') {
                    throw new Exception("Direct Sale #{$freshSale->sale_number} is already cancelled.");
                }

                // Lock invoice if present
                $invoice = null;
                if ($freshSale->invoice_id) {
                    $invoice = Invoice::where('id', $freshSale->invoice_id)->lockForUpdate()->first();
                }

                $stockRestored = false;

                // 2. FIFO Stock Reversal if products are physically returned
                if ($restoreStock && ! $freshSale->stock_reversed) {
                    foreach ($freshSale->items as $item) {
                        if (! empty($item->batch_allocations) && is_array($item->batch_allocations)) {
                            $this->stockService->reverseStockFifo(
                                $item->batch_allocations,
                                $freshSale,
                                $user,
                                "Avoid Direct Sale #{$freshSale->sale_number}: {$reason}"
                            );
                            $stockRestored = true;
                        }
                    }
                }

                // 3. Mark Direct Sale cancelled
                $freshSale->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => $reason,
                    'cancelled_by' => $user->id,
                    'cancelled_at' => now(),
                    'stock_reversed' => $stockRestored || $freshSale->stock_reversed,
                ]);

                // 4. Update Invoice & reverse financial records
                if ($invoice) {
                    $invoice->update([
                        'status' => 'cancelled',
                        'amount_paid' => 0.00,
                        'balance_amount' => 0.00,
                        'cancellation_reason' => $reason,
                        'cancelled_by' => $user->id,
                        'cancelled_at' => now(),
                        'notes' => trim(($invoice->notes ? $invoice->notes."\n" : '')."Cancelled via Avoid Direct Sale: {$reason}"),
                    ]);

                    // Reverse all linked payments
                    foreach ($invoice->payments as $payment) {
                        $payment->update([
                            'status' => 'reversed',
                            'reversed_at' => now(),
                            'reversal_reason' => "Avoid Direct Sale #{$freshSale->sale_number}: {$reason}",
                        ]);
                    }
                }

                // 5. Activity Log Audit
                ActivityLog::log(
                    'avoid_direct_sale',
                    "Cancelled Direct Sale #{$freshSale->sale_number}".($invoice ? " (Invoice #{$invoice->invoice_number})" : '').". Reason: {$reason}".($stockRestored ? ' (FIFO stock batches restored)' : ' (Stock not returned)'),
                    'Sales & Billing',
                    $user->id
                );
            });

            return redirect()->route('billing.avoid-direct-sale.index', ['search' => $directSale->sale_number])
                ->with('success', "Direct Sale #{$directSale->sale_number} has been cancelled successfully and removed from active sales.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', "Cancellation failed: {$e->getMessage()}");
        }
    }
}
