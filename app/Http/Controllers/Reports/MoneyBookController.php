<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DirectSale;
use App\Models\Expense;
use App\Models\ExpensePayment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\BranchContext;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MoneyBookController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $accessibleBranches = BranchContext::getAccessibleBranches($user);

        // Branch scoping
        $branchId = $request->input('branch_id');
        if ($user->isAdmin()) {
            if ($branchId === null) {
                $branchId = BranchContext::getActiveBranchId() ?: 'all';
            }
        } else {
            $branchId = BranchContext::getActiveBranchId();
        }

        // Date filter: defaults to today
        $period = $request->input('period', 'today');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        if ($period === 'today' && empty($dateFrom)) {
            $dateFrom = Carbon::today()->format('Y-m-d');
            $dateTo = Carbon::today()->format('Y-m-d');
        } elseif ($period === 'this_month' && empty($dateFrom)) {
            $dateFrom = Carbon::today()->startOfMonth()->format('Y-m-d');
            $dateTo = Carbon::today()->format('Y-m-d');
        } elseif (empty($dateFrom)) {
            $dateFrom = Carbon::today()->format('Y-m-d');
            $dateTo = Carbon::today()->format('Y-m-d');
        }

        $methodFilter = $request->input('payment_method', 'all');

        // Query payments received on payment_date between dateFrom and dateTo
        $paymentQuery = Payment::with(['invoice.customer', 'invoice.branch', 'customer', 'receipt', 'jobCard.customer', 'jobCard.branch', 'directSale.customer', 'directSale.branch'])
            ->whereDate('payment_date', '>=', $dateFrom)
            ->whereDate('payment_date', '<=', $dateTo);

        if ($branchId && $branchId !== 'all') {
            $paymentQuery->where(function ($q) use ($branchId) {
                $q->whereHas('invoice', fn ($iq) => $iq->where('branch_id', $branchId))
                    ->orWhereHas('jobCard', fn ($jq) => $jq->where('branch_id', $branchId))
                    ->orWhereHas('receipt', fn ($rq) => $rq->where('branch_id', $branchId))
                    ->orWhereHas('directSale', fn ($dq) => $dq->where('branch_id', $branchId));
            });
        }

        if ($methodFilter !== 'all') {
            $paymentQuery->where('payment_method', $methodFilter);
        }

        $allPayments = $paymentQuery->orderBy('payment_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Collapse multi-invoice payment allocations so that the master receipt is shown only once in Money Book
        $processedReceiptIds = [];
        $displayPayments = collect();

        foreach ($allPayments as $p) {
            if ($p->payment_receipt_id) {
                if (in_array($p->payment_receipt_id, $processedReceiptIds)) {
                    continue;
                }
                $processedReceiptIds[] = $p->payment_receipt_id;

                $receipt = $p->receipt;
                $receiptPayments = $allPayments->where('payment_receipt_id', $p->payment_receipt_id);
                $receiptInvoices = $receiptPayments->pluck('invoice.invoice_number')->filter()->unique()->values();

                $receiptEntry = clone $p;
                $receiptEntry->payment_number = $receipt?->receipt_number ?? $p->payment_number;
                $receiptEntry->amount = $receiptPayments->sum('amount');
                if ($receiptInvoices->count() > 1) {
                    $receiptEntry->setRelation('invoice', null); // Displays as Multiple / Split
                }
                $displayPayments->push($receiptEntry);
            } else {
                $displayPayments->push($p);
            }
        }

        $payments = $displayPayments;

        // Query Credit Sales Created in this date range (Invoices issued with remaining credit balance)
        $creditSalesQuery = Invoice::with(['customer', 'branch'])
            ->where('status', '!=', 'cancelled')
            ->where('balance_amount', '>', 0)
            ->whereDate('invoice_date', '>=', $dateFrom)
            ->whereDate('invoice_date', '<=', $dateTo);

        if ($branchId && $branchId !== 'all') {
            $creditSalesQuery->where('branch_id', $branchId);
        }

        $creditSales = $creditSalesQuery->get();

        // Include unbilled Direct Sales with remaining credit balances
        $directCreditSalesQuery = DirectSale::with(['customer', 'branch'])
            ->whereNull('invoice_id')
            ->where('status', '!=', 'cancelled')
            ->whereRaw('grand_total > amount_paid')
            ->whereDate('sale_date', '>=', $dateFrom)
            ->whereDate('sale_date', '<=', $dateTo);

        if ($branchId && $branchId !== 'all') {
            $directCreditSalesQuery->where('branch_id', $branchId);
        }

        $directCreditSales = $directCreditSalesQuery->get();
        foreach ($directCreditSales as $dcs) {
            $dcs->invoice_date = $dcs->sale_date;
            $dcs->invoice_number = $dcs->bill_number;
            $dcs->balance_amount = max(0, (float) $dcs->grand_total - (float) $dcs->amount_paid);
            $dcs->is_bill = true;
        }

        $creditSales = $creditSales->concat($directCreditSales);

        // Calculate summary cards
        $cashReceived = 0.0;
        $cardReceived = 0.0;
        $chequeReceived = 0.0;
        $bankTransferReceived = 0.0;
        $refundsReversals = 0.0;
        $creditPaymentsCollected = 0.0;

        foreach ($payments as $p) {
            $amt = (float) $p->amount;

            if ($p->isReversed()) {
                $refundsReversals += $amt;

                continue;
            }

            $method = strtolower($p->payment_method ?? 'cash');
            if (str_contains($method, 'cash')) {
                $cashReceived += $amt;
            } elseif (str_contains($method, 'card')) {
                $cardReceived += $amt;
            } elseif (str_contains($method, 'cheque')) {
                $chequeReceived += $amt;
            } elseif (str_contains($method, 'bank') || str_contains($method, 'transfer')) {
                $bankTransferReceived += $amt;
            } else {
                $cashReceived += $amt;
            }

            // Check if collection against an invoice issued on an earlier date or marked credit
            if ($p->invoice && $p->invoice->invoice_date && $p->invoice->invoice_date->format('Y-m-d') < $p->payment_date->format('Y-m-d')) {
                $creditPaymentsCollected += $amt;
            }
        }

        $creditSalesCreated = (float) $creditSales->sum('balance_amount');
        $totalLiquidInflow = round($cashReceived + $cardReceived + $bankTransferReceived, 2); // Excludes uncleared cheques per rule

        // Expense Payments Outflows (Only actual payments made, unpaid expenses are excluded)
        $expensePaymentQuery = ExpensePayment::with(['expense.category', 'branch', 'creator'])
            ->whereHas('expense', fn ($q) => $q->where('status', Expense::STATUS_ACTIVE))
            ->whereDate('payment_date', '>=', $dateFrom)
            ->whereDate('payment_date', '<=', $dateTo);

        if ($branchId && $branchId !== 'all') {
            $expensePaymentQuery->where('branch_id', $branchId);
        }

        if ($methodFilter !== 'all') {
            $expensePaymentQuery->where('payment_method', $methodFilter);
        }

        $expensePayments = $expensePaymentQuery->orderBy('payment_date', 'desc')->get();

        $expenseCashOutflow = 0.0;
        $expenseBankOutflow = 0.0;
        $expenseCardOutflow = 0.0;
        $expenseChequeOutflow = 0.0;

        foreach ($expensePayments as $ep) {
            $eAmt = (float) $ep->amount;
            $eMethod = strtolower($ep->payment_method ?? 'cash');
            if (str_contains($eMethod, 'cash')) {
                $expenseCashOutflow += $eAmt;
            } elseif (str_contains($eMethod, 'card')) {
                $expenseCardOutflow += $eAmt;
            } elseif (str_contains($eMethod, 'cheque')) {
                $expenseChequeOutflow += $eAmt;
            } elseif (str_contains($eMethod, 'bank') || str_contains($eMethod, 'transfer')) {
                $expenseBankOutflow += $eAmt;
            } else {
                $expenseCashOutflow += $eAmt;
            }
        }

        $totalExpenseOutflow = round($expenseCashOutflow + $expenseBankOutflow + $expenseCardOutflow + $expenseChequeOutflow, 2);
        $netCashBalance = round($cashReceived - $expenseCashOutflow, 2);

        $selectedBranch = ($branchId && $branchId !== 'all') ? Branch::find($branchId) : null;

        return view('reports.money-book.index', compact(
            'payments',
            'creditSales',
            'expensePayments',
            'dateFrom',
            'dateTo',
            'period',
            'branchId',
            'methodFilter',
            'accessibleBranches',
            'selectedBranch',
            'cashReceived',
            'cardReceived',
            'chequeReceived',
            'bankTransferReceived',
            'refundsReversals',
            'creditSalesCreated',
            'creditPaymentsCollected',
            'totalLiquidInflow',
            'expenseCashOutflow',
            'expenseBankOutflow',
            'totalExpenseOutflow',
            'netCashBalance'
        ));
    }

    public function print(Request $request): View
    {
        $user = $request->user();
        $branchId = $request->input('branch_id', 'all');
        if (! $user->isAdmin()) {
            $branchId = BranchContext::getActiveBranchId();
        }

        $dateFrom = $request->input('date_from', Carbon::today()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::today()->format('Y-m-d'));

        $paymentQuery = Payment::with(['invoice.customer', 'invoice.branch', 'customer', 'jobCard.customer', 'jobCard.branch'])
            ->whereDate('payment_date', '>=', $dateFrom)
            ->whereDate('payment_date', '<=', $dateTo);

        if ($branchId && $branchId !== 'all') {
            $paymentQuery->where(function ($q) use ($branchId) {
                $q->whereHas('invoice', fn ($iq) => $iq->where('branch_id', $branchId))
                    ->orWhereHas('jobCard', fn ($jq) => $jq->where('branch_id', $branchId));
            });
        }

        $payments = $paymentQuery->orderBy('payment_date', 'desc')->get();

        $creditSalesQuery = Invoice::with(['customer', 'branch'])
            ->where('status', '!=', 'cancelled')
            ->where('balance_amount', '>', 0)
            ->whereDate('invoice_date', '>=', $dateFrom)
            ->whereDate('invoice_date', '<=', $dateTo);

        if ($branchId && $branchId !== 'all') {
            $creditSalesQuery->where('branch_id', $branchId);
        }
        $creditSales = $creditSalesQuery->get();

        $cashReceived = (float) $payments->where('status', '!=', 'reversed')->filter(fn ($p) => str_contains(strtolower($p->payment_method), 'cash'))->sum('amount');
        $cardReceived = (float) $payments->where('status', '!=', 'reversed')->filter(fn ($p) => str_contains(strtolower($p->payment_method), 'card'))->sum('amount');
        $chequeReceived = (float) $payments->where('status', '!=', 'reversed')->filter(fn ($p) => str_contains(strtolower($p->payment_method), 'cheque'))->sum('amount');
        $bankTransferReceived = (float) $payments->where('status', '!=', 'reversed')->filter(fn ($p) => str_contains(strtolower($p->payment_method), 'bank') || str_contains(strtolower($p->payment_method), 'transfer'))->sum('amount');
        $creditSalesCreated = (float) $creditSales->sum('balance_amount');

        // Expense Payments Outflows
        $expensePaymentQuery = ExpensePayment::with(['expense.category'])
            ->whereHas('expense', fn ($q) => $q->where('status', Expense::STATUS_ACTIVE))
            ->whereDate('payment_date', '>=', $dateFrom)
            ->whereDate('payment_date', '<=', $dateTo);

        if ($branchId && $branchId !== 'all') {
            $expensePaymentQuery->where('branch_id', $branchId);
        }

        $expensePayments = $expensePaymentQuery->get();
        $expenseCashOutflow = (float) $expensePayments->filter(fn ($p) => str_contains(strtolower($p->payment_method), 'cash'))->sum('amount');
        $expenseBankOutflow = (float) $expensePayments->filter(fn ($p) => str_contains(strtolower($p->payment_method), 'bank') || str_contains(strtolower($p->payment_method), 'card'))->sum('amount');
        $totalExpenseOutflow = (float) $expensePayments->sum('amount');
        $netCashBalance = round($cashReceived - $expenseCashOutflow, 2);

        $selectedBranch = ($branchId && $branchId !== 'all') ? Branch::find($branchId) : null;

        return view('reports.money-book.print', compact(
            'payments',
            'creditSales',
            'expensePayments',
            'dateFrom',
            'dateTo',
            'selectedBranch',
            'cashReceived',
            'cardReceived',
            'chequeReceived',
            'bankTransferReceived',
            'creditSalesCreated',
            'expenseCashOutflow',
            'expenseBankOutflow',
            'totalExpenseOutflow',
            'netCashBalance'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $user = $request->user();
        $branchId = $request->input('branch_id', 'all');
        if (! $user->isAdmin()) {
            $branchId = BranchContext::getActiveBranchId();
        }

        $dateFrom = $request->input('date_from', Carbon::today()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::today()->format('Y-m-d'));

        $paymentQuery = Payment::with(['invoice.customer', 'invoice.branch', 'customer', 'receipt', 'jobCard.customer', 'jobCard.branch', 'directSale.customer', 'directSale.branch'])
            ->whereDate('payment_date', '>=', $dateFrom)
            ->whereDate('payment_date', '<=', $dateTo);

        if ($branchId && $branchId !== 'all') {
            $paymentQuery->where(function ($q) use ($branchId) {
                $q->whereHas('invoice', fn ($iq) => $iq->where('branch_id', $branchId))
                    ->orWhereHas('jobCard', fn ($jq) => $jq->where('branch_id', $branchId))
                    ->orWhereHas('receipt', fn ($rq) => $rq->where('branch_id', $branchId))
                    ->orWhereHas('directSale', fn ($dq) => $dq->where('branch_id', $branchId));
            });
        }

        $allPayments = $paymentQuery->orderBy('payment_date', 'desc')->get();
        $processedReceiptIds = [];
        $displayPayments = collect();

        foreach ($allPayments as $p) {
            if ($p->payment_receipt_id) {
                if (in_array($p->payment_receipt_id, $processedReceiptIds)) {
                    continue;
                }
                $processedReceiptIds[] = $p->payment_receipt_id;
                $receiptPayments = $allPayments->where('payment_receipt_id', $p->payment_receipt_id);
                $receiptInvoices = $receiptPayments->pluck('invoice.invoice_number')->filter()->unique()->values();

                $receiptEntry = clone $p;
                $receiptEntry->payment_number = $p->receipt?->receipt_number ?? $p->payment_number;
                $receiptEntry->amount = $receiptPayments->sum('amount');
                if ($receiptInvoices->count() > 1) {
                    $receiptEntry->setRelation('invoice', null);
                }
                $displayPayments->push($receiptEntry);
            } else {
                $displayPayments->push($p);
            }
        }
        $payments = $displayPayments;
        $fileName = 'money_book_'.now()->format('Y_m_d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        return response()->streamDownload(function () use ($payments, $dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['MONEY BOOK', "Period: {$dateFrom} to {$dateTo}"]);
            fputcsv($file, []);

            fputcsv($file, [
                'Payment Date',
                'Payment / Receipt #',
                'Invoice / Bill #',
                'Customer / Company',
                'Payment Method',
                'Reference / Cheque Details',
                'Amount Received (LKR)',
                'Status',
                'Notes',
            ]);

            foreach ($payments as $p) {
                $invOrJob = $p->invoice?->invoice_number ?? ($p->directSale ? "Bill #{$p->directSale->bill_number}" : ($p->jobCard ? "Job #{$p->jobCard->job_number} (Advance)" : 'Multiple Invoices'));
                $cust = $p->invoice?->customer?->display_name ?? ($p->directSale?->customer?->display_name ?? ($p->jobCard?->customer?->display_name ?? ($p->customer?->display_name ?? 'Guest Customer')));
                fputcsv($file, [
                    $p->payment_date?->format('Y-m-d') ?? '-',
                    $p->payment_number,
                    $invOrJob,
                    $cust,
                    ucfirst(str_replace('_', ' ', $p->payment_method ?? 'cash')),
                    $p->reference_number ?? '-',
                    number_format($p->amount, 2, '.', ''),
                    ucfirst($p->status ?? 'completed'),
                ]);
            }

            fclose($file);
        }, $fileName, $headers);
    }
}
