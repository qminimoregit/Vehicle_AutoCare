<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerCreditAccount;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CreditManagementController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $status = $request->input('status', 'all');
        $type = $request->input('type', 'all');

        $query = CustomerCreditAccount::with(['customer.vehicles', 'customer.invoices']);

        if ($search !== '') {
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($type === 'company') {
            $query->whereHas('customer', fn ($q) => $q->where('customer_type', 'company'));
        } elseif ($type === 'individual') {
            $query->whereHas('customer', fn ($q) => $q->where('customer_type', '!=', 'company'));
        }

        $accounts = $query->orderBy('id', 'desc')->get();

        // Calculate summary metrics
        $totalCreditLimit = (float) $accounts->sum('credit_limit');
        $totalOutstanding = (float) $accounts->sum('outstanding_balance');
        $totalAvailableCredit = (float) $accounts->sum('available_credit');
        $activeAccountsCount = $accounts->where('status', 'active')->count();

        // Compute overall aging buckets
        $overallAging = [
            'current' => 0.0,
            'days_1_30' => 0.0,
            'days_31_60' => 0.0,
            'days_61_90' => 0.0,
            'over_90' => 0.0,
        ];

        foreach ($accounts as $acc) {
            $summary = $acc->aging_summary;
            $overallAging['current'] += $summary['current'];
            $overallAging['days_1_30'] += $summary['days_1_30'];
            $overallAging['days_31_60'] += $summary['days_31_60'];
            $overallAging['days_61_90'] += $summary['days_61_90'];
            $overallAging['over_90'] += $summary['over_90'];
        }

        return view('reports.credit-management.index', compact(
            'accounts',
            'search',
            'status',
            'type',
            'totalCreditLimit',
            'totalOutstanding',
            'totalAvailableCredit',
            'activeAccountsCount',
            'overallAging'
        ));
    }

    /**
     * Show form to create/link credit account to an existing customer.
     */
    public function create(): View
    {
        $existingCustomerIds = CustomerCreditAccount::pluck('customer_id')->toArray();
        $customers = Customer::whereNotIn('id', $existingCustomerIds)
            ->orderBy('customer_name')
            ->get();

        return view('reports.credit-management.create', compact('customers'));
    }

    /**
     * Store new customer credit account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id|unique:customer_credit_accounts,customer_id',
            'credit_limit' => 'required|numeric|min:0',
            'payment_terms_days' => 'required|integer|min:0|max:365',
            'opening_balance' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,suspended,closed',
            'notes' => 'nullable|string',
        ]);

        $validated['opening_balance'] = $validated['opening_balance'] ?? 0.00;
        $validated['created_by'] = $request->user()->id;

        CustomerCreditAccount::create($validated);

        return redirect()->route('reports.credit-management.index')
            ->with('success', 'Customer credit account registered successfully.');
    }

    /**
     * View customer credit profile and fleet breakdown.
     */
    public function show(CustomerCreditAccount $creditAccount): View
    {
        $creditAccount->loadMissing(['customer.vehicles.vehicleType', 'customer.invoices.jobCard.vehicle']);
        $customer = $creditAccount->customer;

        // Invoices for this customer/company
        $invoices = $customer->invoices()
            ->with(['jobCard.vehicle'])
            ->where('status', '!=', 'cancelled')
            ->orderBy('invoice_date', 'desc')
            ->get();

        // Payments recorded for this customer
        $payments = Payment::where(function ($q) use ($customer, $invoices) {
            $q->where('customer_id', $customer->id)
                ->orWhereIn('invoice_id', $invoices->pluck('id'));
        })->where('status', '!=', 'reversed')
            ->orderBy('payment_date', 'desc')
            ->get();

        $aging = $creditAccount->aging_summary;

        return view('reports.credit-management.show', compact(
            'creditAccount',
            'customer',
            'invoices',
            'payments',
            'aging'
        ));
    }

    /**
     * Update credit account settings.
     */
    public function update(Request $request, CustomerCreditAccount $creditAccount): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403, 'Admin authorization required.');

        $validated = $request->validate([
            'credit_limit' => 'required|numeric|min:0',
            'payment_terms_days' => 'required|integer|min:0|max:365',
            'opening_balance' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,suspended,closed',
            'notes' => 'nullable|string',
        ]);

        $creditAccount->update($validated);

        return redirect()->route('reports.credit-management.show', $creditAccount)
            ->with('success', 'Credit account updated successfully.');
    }

    /**
     * Generate Monthly Company Statement.
     */
    public function statement(Request $request, CustomerCreditAccount $creditAccount): View
    {
        $customer = $creditAccount->customer;
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        $statementStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $statementEnd = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // 1. Opening balance as of start of selected month:
        // Prior opening balance + prior invoices balance
        $priorInvoicesTotal = (float) $customer->invoices()
            ->where('status', '!=', 'cancelled')
            ->where('invoice_date', '<', $statementStart)
            ->sum('grand_total');

        $priorPaymentsTotal = (float) Payment::where('status', '!=', 'reversed')
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id)
                    ->orWhereHas('invoice', fn ($iq) => $iq->where('customer_id', $customer->id));
            })
            ->where('payment_date', '<', $statementStart)
            ->sum('amount');

        $openingBalance = round((float) $creditAccount->opening_balance + max(0, $priorInvoicesTotal - $priorPaymentsTotal), 2);

        // 2. Invoices issued during this month across all company vehicles
        $monthInvoices = $customer->invoices()
            ->with(['jobCard.vehicle'])
            ->where('status', '!=', 'cancelled')
            ->whereBetween('invoice_date', [$statementStart, $statementEnd])
            ->orderBy('invoice_date', 'asc')
            ->get();

        $monthInvoicesTotal = (float) $monthInvoices->sum('grand_total');

        // 3. Payments made during this month
        $monthPayments = Payment::with('invoice')
            ->where('status', '!=', 'reversed')
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id)
                    ->orWhereHas('invoice', fn ($iq) => $iq->where('customer_id', $customer->id));
            })
            ->whereBetween('payment_date', [$statementStart, $statementEnd])
            ->orderBy('payment_date', 'asc')
            ->get();

        $monthPaymentsTotal = (float) $monthPayments->sum('amount');

        // 4. Closing balance as of end of month
        $closingBalance = round(max(0, $openingBalance + $monthInvoicesTotal - $monthPaymentsTotal), 2);

        return view('reports.credit-management.statement', compact(
            'creditAccount',
            'customer',
            'month',
            'year',
            'statementStart',
            'statementEnd',
            'openingBalance',
            'monthInvoices',
            'monthInvoicesTotal',
            'monthPayments',
            'monthPaymentsTotal',
            'closingBalance'
        ));
    }

    /**
     * Print Monthly Company Statement.
     */
    public function statementPrint(Request $request, CustomerCreditAccount $creditAccount): View
    {
        $customer = $creditAccount->customer;
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        $statementStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $statementEnd = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $priorInvoicesTotal = (float) $customer->invoices()
            ->where('status', '!=', 'cancelled')
            ->where('invoice_date', '<', $statementStart)
            ->sum('grand_total');

        $priorPaymentsTotal = (float) Payment::where('status', '!=', 'reversed')
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id)
                    ->orWhereHas('invoice', fn ($iq) => $iq->where('customer_id', $customer->id));
            })
            ->where('payment_date', '<', $statementStart)
            ->sum('amount');

        $openingBalance = round((float) $creditAccount->opening_balance + max(0, $priorInvoicesTotal - $priorPaymentsTotal), 2);

        $monthInvoices = $customer->invoices()
            ->with(['jobCard.vehicle'])
            ->where('status', '!=', 'cancelled')
            ->whereBetween('invoice_date', [$statementStart, $statementEnd])
            ->orderBy('invoice_date', 'asc')
            ->get();
        $monthInvoicesTotal = (float) $monthInvoices->sum('grand_total');

        $monthPayments = Payment::with('invoice')
            ->where('status', '!=', 'reversed')
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id)
                    ->orWhereHas('invoice', fn ($iq) => $iq->where('customer_id', $customer->id));
            })
            ->whereBetween('payment_date', [$statementStart, $statementEnd])
            ->orderBy('payment_date', 'asc')
            ->get();
        $monthPaymentsTotal = (float) $monthPayments->sum('amount');

        $closingBalance = round(max(0, $openingBalance + $monthInvoicesTotal - $monthPaymentsTotal), 2);

        return view('reports.credit-management.statement-print', compact(
            'creditAccount',
            'customer',
            'month',
            'year',
            'statementStart',
            'statementEnd',
            'openingBalance',
            'monthInvoices',
            'monthInvoicesTotal',
            'monthPayments',
            'monthPaymentsTotal',
            'closingBalance'
        ));
    }

    /**
     * Export Monthly Statement to CSV.
     */
    public function statementExport(Request $request, CustomerCreditAccount $creditAccount): StreamedResponse
    {
        $customer = $creditAccount->customer;
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        $statementStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $statementEnd = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $monthInvoices = $customer->invoices()
            ->with(['jobCard.vehicle'])
            ->where('status', '!=', 'cancelled')
            ->whereBetween('invoice_date', [$statementStart, $statementEnd])
            ->orderBy('invoice_date', 'asc')
            ->get();

        $monthPayments = Payment::where('status', '!=', 'reversed')
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id)
                    ->orWhereHas('invoice', fn ($iq) => $iq->where('customer_id', $customer->id));
            })
            ->whereBetween('payment_date', [$statementStart, $statementEnd])
            ->orderBy('payment_date', 'asc')
            ->get();

        $fileName = 'credit_statement_'.str_replace(' ', '_', $customer->customer_name).'_'.$year.'_'.$month.'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        return response()->streamDownload(function () use ($customer, $monthInvoices, $monthPayments, $month, $year) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['MONTHLY COMPANY CREDIT STATEMENT', "Customer: {$customer->display_name}", "Month: {$year}-{$month}"]);
            fputcsv($file, []);

            fputcsv($file, ['INVOICES ISSUED DURING MONTH']);
            fputcsv($file, ['Date', 'Vehicle', 'Invoice No', 'Amount (LKR)', 'Paid (LKR)', 'Balance Due (LKR)']);

            foreach ($monthInvoices as $inv) {
                fputcsv($file, [
                    $inv->invoice_date?->format('Y-m-d') ?? '-',
                    $inv->jobCard?->vehicle?->vehicle_number ?? '-',
                    $inv->invoice_number,
                    number_format($inv->grand_total, 2, '.', ''),
                    number_format($inv->amount_paid, 2, '.', ''),
                    number_format($inv->balance_amount, 2, '.', ''),
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['PAYMENTS RECEIVED DURING MONTH']);
            fputcsv($file, ['Date', 'Receipt / Payment No', 'Method', 'Reference', 'Amount Paid (LKR)']);

            foreach ($monthPayments as $p) {
                fputcsv($file, [
                    $p->payment_date?->format('Y-m-d') ?? '-',
                    $p->payment_number,
                    ucfirst($p->payment_method),
                    $p->reference_number ?? '-',
                    number_format($p->amount, 2, '.', ''),
                ]);
            }

            fclose($file);
        }, $fileName, $headers);
    }

    /**
     * API check for Credit Limit Validation.
     * Checks if customer has credit account, and if proposed additional amount exceeds credit limit.
     */
    public function checkCreditLimit(Request $request): JsonResponse
    {
        $customerId = $request->input('customer_id');
        $proposedAmount = (float) $request->input('amount', 0.0);

        if (! $customerId) {
            return response()->json(['has_credit' => false, 'exceeds' => false]);
        }

        $creditAccount = CustomerCreditAccount::where('customer_id', $customerId)
            ->where('status', 'active')
            ->first();

        if (! $creditAccount) {
            return response()->json([
                'has_credit' => false,
                'exceeds' => false,
                'message' => 'Customer does not have an active credit account.',
            ]);
        }

        $limit = (float) $creditAccount->credit_limit;
        $outstanding = $creditAccount->outstanding_balance;
        $available = $creditAccount->available_credit;
        $exceeds = ($limit > 0) && (($outstanding + $proposedAmount) > $limit);

        return response()->json([
            'has_credit' => true,
            'credit_limit' => $limit,
            'outstanding_balance' => $outstanding,
            'available_credit' => $available,
            'exceeds' => $exceeds,
            'message' => $exceeds ? 'Credit limit exceeded.' : 'Credit within approved limit.',
        ]);
    }
}
