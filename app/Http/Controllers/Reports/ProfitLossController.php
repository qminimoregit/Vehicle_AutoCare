<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Models\StockMovement;
use App\Services\BranchContext;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfitLossController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->isAdmin(), 403, 'Unauthorized access to Profit & Loss financial statements.');

        $accessibleBranches = BranchContext::getAccessibleBranches($user);
        $branchId = $request->input('branch_id', 'all');

        $period = $request->input('period', 'this_month');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        if ($period === 'today') {
            $dateFrom = Carbon::today()->format('Y-m-d');
            $dateTo = Carbon::today()->format('Y-m-d');
        } elseif ($period === 'this_month' && empty($dateFrom)) {
            $dateFrom = Carbon::today()->startOfMonth()->format('Y-m-d');
            $dateTo = Carbon::today()->format('Y-m-d');
        } elseif ($period === 'last_month' && empty($dateFrom)) {
            $dateFrom = Carbon::today()->subMonth()->startOfMonth()->format('Y-m-d');
            $dateTo = Carbon::today()->subMonth()->endOfMonth()->format('Y-m-d');
        } elseif (empty($dateFrom)) {
            $dateFrom = Carbon::today()->startOfMonth()->format('Y-m-d');
            $dateTo = Carbon::today()->format('Y-m-d');
        }

        $reportData = $this->calculateProfitAndLoss($dateFrom, $dateTo, $branchId);
        $selectedBranch = ($branchId && $branchId !== 'all') ? Branch::find($branchId) : null;

        return view('reports.profit-loss.index', array_merge($reportData, [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'period' => $period,
            'branchId' => $branchId,
            'accessibleBranches' => $accessibleBranches,
            'selectedBranch' => $selectedBranch,
        ]));
    }

    public function print(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->isAdmin(), 403, 'Unauthorized access.');

        $branchId = $request->input('branch_id', 'all');
        $dateFrom = $request->input('date_from', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::today()->format('Y-m-d'));

        $reportData = $this->calculateProfitAndLoss($dateFrom, $dateTo, $branchId);
        $selectedBranch = ($branchId && $branchId !== 'all') ? Branch::find($branchId) : null;

        return view('reports.profit-loss.print', array_merge($reportData, [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'selectedBranch' => $selectedBranch,
        ]));
    }

    public function export(Request $request): StreamedResponse
    {
        $user = $request->user();
        abort_unless($user->isAdmin(), 403, 'Unauthorized access.');

        $branchId = $request->input('branch_id', 'all');
        $dateFrom = $request->input('date_from', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::today()->format('Y-m-d'));

        $data = $this->calculateProfitAndLoss($dateFrom, $dateTo, $branchId);
        $fileName = 'profit_and_loss_'.now()->format('Y_m_d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        return response()->streamDownload(function () use ($data, $dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['PROFIT & LOSS STATEMENT', "Period: {$dateFrom} to {$dateTo}"]);
            fputcsv($file, []);

            // 1. REVENUE
            fputcsv($file, ['REVENUE', 'Amount (LKR)']);
            fputcsv($file, ['  Direct Sales Revenue', number_format($data['directSalesRevenue'], 2, '.', '')]);
            fputcsv($file, ['  Service Sales Revenue', number_format($data['serviceSalesRevenue'], 2, '.', '')]);
            if ($data['otherRevenue'] > 0) {
                fputcsv($file, ['  Other Operating Revenue', number_format($data['otherRevenue'], 2, '.', '')]);
            }
            fputcsv($file, ['  Gross Revenue', number_format($data['grossRevenue'], 2, '.', '')]);
            fputcsv($file, ['  Less: Sales Discounts', number_format($data['salesDiscounts'], 2, '.', '')]);
            fputcsv($file, ['NET REVENUE', number_format($data['netRevenue'], 2, '.', '')]);
            fputcsv($file, []);

            // 2. COST OF SALES
            fputcsv($file, ['COST OF SALES', 'Amount (LKR)']);
            fputcsv($file, ['  Product Cost of Goods Sold (FIFO)', number_format($data['cogsProduct'], 2, '.', '')]);
            fputcsv($file, ['  Direct Service Costs', number_format($data['directServiceCosts'], 2, '.', '')]);
            fputcsv($file, ['TOTAL COST OF SALES', number_format($data['totalCostOfSales'], 2, '.', '')]);
            fputcsv($file, []);

            // GROSS PROFIT
            fputcsv($file, ['GROSS PROFIT', number_format($data['grossProfit'], 2, '.', '')]);
            fputcsv($file, ['Gross Profit Margin %', number_format($data['grossProfitMargin'], 2, '.', '').'%']);
            fputcsv($file, []);

            // 3. OPERATING EXPENSES
            fputcsv($file, ['OPERATING EXPENSES', 'Amount (LKR)']);
            fputcsv($file, ['  Employee Salaries', number_format($data['salaryExpenses'], 2, '.', '')]);
            foreach ($data['categorizedExpenses'] as $catName => $amount) {
                fputcsv($file, ["  {$catName}", number_format($amount, 2, '.', '')]);
            }
            fputcsv($file, ['TOTAL OPERATING EXPENSES', number_format($data['totalOperatingExpenses'], 2, '.', '')]);
            fputcsv($file, []);

            // NET OPERATING PROFIT
            fputcsv($file, ['NET OPERATING PROFIT', number_format($data['netProfit'], 2, '.', '')]);
            fputcsv($file, ['Net Profit Margin %', number_format($data['netProfitMargin'], 2, '.', '').'%']);

            fclose($file);
        }, $fileName, $headers);
    }

    /**
     * Reconcile and calculate Profit & Loss strictly using real database records:
     * - Revenue: Invoices minus discounts (excl cancelled)
     * - COGS: FIFO StockMovements (direct_sale + job_card_issue - issue_reversal)
     * - Operating Expenses: Expense records (excl advances) + Payroll items
     */
    protected function calculateProfitAndLoss(string $dateFrom, string $dateTo, string|int $branchId): array
    {
        $startDate = Carbon::parse($dateFrom)->startOfDay();
        $endDate = Carbon::parse($dateTo)->endOfDay();

        // 1. INVOICE REVENUE
        $invoiceQuery = Invoice::with(['directSale'])
            ->where('status', '!=', 'cancelled')
            ->whereDate('invoice_date', '>=', $dateFrom)
            ->whereDate('invoice_date', '<=', $dateTo);

        if ($branchId && $branchId !== 'all') {
            $invoiceQuery->where('branch_id', $branchId);
        }

        $invoices = $invoiceQuery->get();

        $directSalesRevenue = 0.0;
        $serviceSalesRevenue = 0.0;
        $otherRevenue = 0.0;
        $salesDiscounts = 0.0;

        foreach ($invoices as $inv) {
            $sub = (float) $inv->subtotal;
            $disc = (float) $inv->discount_amount;
            $salesDiscounts += $disc;

            $isDirect = ($inv->invoice_type === 'direct_sale' || $inv->directSale !== null);
            if ($isDirect) {
                $directSalesRevenue += $sub;
            } elseif ($inv->invoice_type === 'job_card' || $inv->job_card_id !== null) {
                $serviceSalesRevenue += $sub;
            } else {
                $otherRevenue += $sub;
            }
        }

        $grossRevenue = $directSalesRevenue + $serviceSalesRevenue + $otherRevenue;
        $netRevenue = round(max(0, $grossRevenue - $salesDiscounts), 2);

        // 2. COST OF GOODS SOLD (FIFO Batch Movements)
        $movementQuery = StockMovement::whereBetween('created_at', [$startDate, $endDate]);

        if ($branchId && $branchId !== 'all') {
            $movementQuery->where('branch_id', $branchId);
        }

        $movements = $movementQuery->whereIn('movement_type', ['direct_sale', 'job_card_issue', 'issue_reversal'])->get();

        $cogsProduct = 0.0;
        foreach ($movements as $m) {
            $qty = (float) $m->quantity;
            $cost = (float) $m->unit_cost;

            if ($m->movement_type === 'issue_reversal') {
                // Reversal reduces COGS
                $cogsProduct -= ($qty * $cost);
            } else {
                // Negative quantity for stock deduction
                $cogsProduct += (abs($qty) * $cost);
            }
        }
        $cogsProduct = round(max(0, $cogsProduct), 2);

        $directServiceCosts = 0.0; // Currently in-house labor, direct materials captured in COGS
        $totalCostOfSales = round($cogsProduct + $directServiceCosts, 2);

        // GROSS PROFIT
        $grossProfit = round($netRevenue - $totalCostOfSales, 2);
        $grossProfitMargin = $netRevenue > 0 ? round(($grossProfit / $netRevenue) * 100, 2) : 0.0;

        // 3. EMPLOYEE SALARIES
        // Gather salary expenses from PayrollPeriods matching the date range or payments in period
        $startYear = (int) $startDate->year;
        $startMonth = (int) $startDate->month;
        $endYear = (int) $endDate->year;
        $endMonth = (int) $endDate->month;

        $salaryExpenses = (float) PayrollItem::whereHas('period', function ($pq) use ($startYear, $startMonth, $endYear, $endMonth) {
            $pq->where(function ($sub) use ($startYear, $startMonth, $endYear, $endMonth) {
                $sub->where('year', '>=', $startYear)
                    ->where('year', '<=', $endYear)
                    ->where('month', '>=', $startMonth)
                    ->where('month', '<=', $endMonth);
            })->where('status', '!=', PayrollPeriod::STATUS_DRAFT);
        })->sum('gross_salary');

        // 4. OPERATING EXPENSES (Exclude salary advances and raw GRN payments)
        $expenseQuery = Expense::with('category')
            ->where('status', Expense::STATUS_ACTIVE)
            ->whereNull('employee_advance_id')
            ->whereDate('expense_date', '>=', $dateFrom)
            ->whereDate('expense_date', '<=', $dateTo);

        if ($branchId && $branchId !== 'all') {
            $expenseQuery->where('branch_id', $branchId);
        }

        $expenses = $expenseQuery->get();

        // Categorize expenses: Electricity, Water, Telephone / Internet, Repairs & Maintenance, Other
        $categorizedExpenses = [
            'Electricity' => 0.0,
            'Water' => 0.0,
            'Telephone / Internet' => 0.0,
            'Repairs & Maintenance' => 0.0,
            'Other Operating Expenses' => 0.0,
        ];

        foreach ($expenses as $exp) {
            $catName = $exp->category ? strtolower($exp->category->name) : 'other';
            $amt = (float) $exp->amount;

            // Skip salary advance if tagged under category name
            if (str_contains($catName, 'salary advance') || str_contains($catName, 'advance')) {
                continue;
            }

            if (str_contains($catName, 'electricity') || str_contains($catName, 'power') || str_contains($catName, 'ceb')) {
                $categorizedExpenses['Electricity'] += $amt;
            } elseif (str_contains($catName, 'water')) {
                $categorizedExpenses['Water'] += $amt;
            } elseif (str_contains($catName, 'telephone') || str_contains($catName, 'internet') || str_contains($catName, 'telecom') || str_contains($catName, 'wifi')) {
                $categorizedExpenses['Telephone / Internet'] += $amt;
            } elseif (str_contains($catName, 'repair') || str_contains($catName, 'maintenance')) {
                $categorizedExpenses['Repairs & Maintenance'] += $amt;
            } else {
                $categorizedExpenses['Other Operating Expenses'] += $amt;
            }
        }

        $totalOperatingExpenses = round($salaryExpenses + array_sum($categorizedExpenses), 2);

        // NET PROFIT
        $netProfit = round($grossProfit - $totalOperatingExpenses, 2);
        $netProfitMargin = $netRevenue > 0 ? round(($netProfit / $netRevenue) * 100, 2) : 0.0;

        return compact(
            'directSalesRevenue',
            'serviceSalesRevenue',
            'otherRevenue',
            'grossRevenue',
            'salesDiscounts',
            'netRevenue',
            'cogsProduct',
            'directServiceCosts',
            'totalCostOfSales',
            'grossProfit',
            'grossProfitMargin',
            'salaryExpenses',
            'categorizedExpenses',
            'totalOperatingExpenses',
            'netProfit',
            'netProfitMargin'
        );
    }
}
