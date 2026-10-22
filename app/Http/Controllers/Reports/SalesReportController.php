<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DirectSale;
use App\Models\Invoice;
use App\Services\BranchContext;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $accessibleBranches = BranchContext::getAccessibleBranches($user);

        // Branch filter
        $branchId = $request->input('branch_id');
        if ($user->isAdmin()) {
            if ($branchId === null) {
                $branchId = BranchContext::getActiveBranchId() ?: 'all';
            }
        } else {
            $branchId = BranchContext::getActiveBranchId();
        }

        // Date range filters
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

        $saleType = $request->input('sale_type', 'all');
        $paymentStatus = $request->input('payment_status', 'all');
        $search = trim((string) $request->input('search'));

        // Query active (non-cancelled) invoices
        $query = Invoice::with(['customer', 'jobCard.vehicle', 'directSale', 'branch'])
            ->where('status', '!=', 'cancelled')
            ->whereDate('invoice_date', '>=', $dateFrom)
            ->whereDate('invoice_date', '<=', $dateTo);

        if ($branchId && $branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        // Filter by sale type
        if ($saleType === 'direct_sale') {
            $query->where(function ($q) {
                $q->where('invoice_type', 'direct_sale')
                    ->orWhereHas('directSale');
            });
        } elseif ($saleType === 'service_sale') {
            $query->where(function ($q) {
                $q->where('invoice_type', 'job_card')
                    ->orWhereNotNull('job_card_id')
                    ->orWhereHas('jobCard');
            });
        } elseif ($saleType === 'credit_sale') {
            $query->where('balance_amount', '>', 0)
                ->where('amount_paid', '<', \DB::raw('grand_total'));
        }

        // Filter by payment status
        if ($paymentStatus === 'paid') {
            $query->where('balance_amount', '<=', 0.0001);
        } elseif ($paymentStatus === 'partially_paid') {
            $query->where('balance_amount', '>', 0.0001)
                ->where('amount_paid', '>', 0.0001);
        } elseif ($paymentStatus === 'unpaid') {
            $query->where('amount_paid', '<=', 0.0001)
                ->where('balance_amount', '>', 0.0001);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('customer_name', 'like', "%{$search}%")
                            ->orWhere('company_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('jobCard.vehicle', function ($vq) use ($search) {
                        $vq->where('license_plate', 'like', "%{$search}%");
                    });
            });
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->orderBy('id', 'desc')->get();

        // Include Direct Sales that have not yet generated an official invoice
        if ($saleType !== 'service_sale') {
            $directSalesQuery = DirectSale::with(['customer', 'branch'])
                ->whereNull('invoice_id')
                ->where('status', '!=', 'cancelled')
                ->whereDate('sale_date', '>=', $dateFrom)
                ->whereDate('sale_date', '<=', $dateTo);

            if ($branchId && $branchId !== 'all') {
                $directSalesQuery->where('branch_id', $branchId);
            }

            if ($saleType === 'credit_sale') {
                $directSalesQuery->whereRaw('grand_total > amount_paid');
            }

            if ($paymentStatus === 'paid') {
                $directSalesQuery->whereRaw('amount_paid >= grand_total');
            } elseif ($paymentStatus === 'partially_paid') {
                $directSalesQuery->whereRaw('amount_paid > 0.0001 AND amount_paid < grand_total');
            } elseif ($paymentStatus === 'unpaid') {
                $directSalesQuery->whereRaw('amount_paid <= 0.0001 AND grand_total > 0.0001');
            }

            if ($search !== '') {
                $directSalesQuery->where(function ($q) use ($search) {
                    $q->where('bill_number', 'like', "%{$search}%")
                        ->orWhere('sale_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($cq) use ($search) {
                            $cq->where('customer_name', 'like', "%{$search}%")
                                ->orWhere('company_name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                });
            }

            $unbilledDirectSales = $directSalesQuery->get();
            foreach ($unbilledDirectSales as $ds) {
                $ds->invoice_date = $ds->sale_date;
                $ds->invoice_number = $ds->bill_number;
                $ds->balance_amount = max(0, (float) $ds->grand_total - (float) $ds->amount_paid);
                $ds->is_bill = true;
                $ds->invoice_type = 'direct_sale';
                $ds->jobCard = null;
            }

            $invoices = $invoices->concat($unbilledDirectSales)->sortByDesc(fn ($item) => $item->invoice_date ?? $item->sale_date)->values();
        }

        // Calculate Summary Cards
        $totalDirectSales = 0.0;
        $totalServiceSales = 0.0;
        $totalSales = 0.0;
        $totalCollected = 0.0;
        $totalOutstanding = 0.0;

        foreach ($invoices as $inv) {
            $grand = (float) $inv->grand_total;
            $paid = (float) $inv->amount_paid;
            $balance = (float) $inv->balance_amount;

            $totalSales += $grand;
            $totalCollected += $paid;
            $totalOutstanding += $balance;

            $isDirect = ($inv->invoice_type === 'direct_sale' || ($inv->is_bill ?? false) || $inv->directSale !== null);
            if ($isDirect) {
                $totalDirectSales += $grand;
            } else {
                $totalServiceSales += $grand;
            }
        }

        $selectedBranch = ($branchId && $branchId !== 'all') ? Branch::find($branchId) : null;

        return view('reports.sales.index', compact(
            'invoices',
            'dateFrom',
            'dateTo',
            'period',
            'saleType',
            'paymentStatus',
            'branchId',
            'search',
            'accessibleBranches',
            'selectedBranch',
            'totalDirectSales',
            'totalServiceSales',
            'totalSales',
            'totalCollected',
            'totalOutstanding'
        ));
    }

    public function print(Request $request): View
    {
        $user = $request->user();
        $branchId = $request->input('branch_id', 'all');
        if (! $user->isAdmin()) {
            $branchId = BranchContext::getActiveBranchId();
        }

        $dateFrom = $request->input('date_from', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::today()->format('Y-m-d'));
        $saleType = $request->input('sale_type', 'all');
        $paymentStatus = $request->input('payment_status', 'all');

        $query = Invoice::with(['customer', 'jobCard.vehicle', 'directSale', 'branch'])
            ->where('status', '!=', 'cancelled')
            ->whereDate('invoice_date', '>=', $dateFrom)
            ->whereDate('invoice_date', '<=', $dateTo);

        if ($branchId && $branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        if ($saleType === 'direct_sale') {
            $query->where(fn ($q) => $q->where('invoice_type', 'direct_sale')->orWhereHas('directSale'));
        } elseif ($saleType === 'service_sale') {
            $query->where(fn ($q) => $q->where('invoice_type', 'job_card')->orWhereNotNull('job_card_id')->orWhereHas('jobCard'));
        } elseif ($saleType === 'credit_sale') {
            $query->where('balance_amount', '>', 0);
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->get();

        if ($saleType !== 'service_sale') {
            $directSalesQuery = DirectSale::with(['customer', 'branch'])
                ->whereNull('invoice_id')
                ->where('status', '!=', 'cancelled')
                ->whereDate('sale_date', '>=', $dateFrom)
                ->whereDate('sale_date', '<=', $dateTo);

            if ($branchId && $branchId !== 'all') {
                $directSalesQuery->where('branch_id', $branchId);
            }

            if ($saleType === 'credit_sale') {
                $directSalesQuery->whereRaw('grand_total > amount_paid');
            }

            $unbilledDirectSales = $directSalesQuery->get();
            foreach ($unbilledDirectSales as $ds) {
                $ds->invoice_date = $ds->sale_date;
                $ds->invoice_number = $ds->bill_number;
                $ds->balance_amount = max(0, (float) $ds->grand_total - (float) $ds->amount_paid);
                $ds->is_bill = true;
                $ds->invoice_type = 'direct_sale';
                $ds->jobCard = null;
            }

            $invoices = $invoices->concat($unbilledDirectSales)->sortByDesc(fn ($item) => $item->invoice_date ?? $item->sale_date)->values();
        }

        $totalSales = (float) $invoices->sum('grand_total');
        $totalCollected = (float) $invoices->sum('amount_paid');
        $totalOutstanding = (float) $invoices->sum('balance_amount');
        $selectedBranch = ($branchId && $branchId !== 'all') ? Branch::find($branchId) : null;

        return view('reports.sales.print', compact(
            'invoices',
            'dateFrom',
            'dateTo',
            'saleType',
            'totalSales',
            'totalCollected',
            'totalOutstanding',
            'selectedBranch'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $user = $request->user();
        $branchId = $request->input('branch_id', 'all');
        if (! $user->isAdmin()) {
            $branchId = BranchContext::getActiveBranchId();
        }

        $dateFrom = $request->input('date_from', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::today()->format('Y-m-d'));
        $saleType = $request->input('sale_type', 'all');

        $query = Invoice::with(['customer', 'jobCard.vehicle', 'directSale', 'branch'])
            ->where('status', '!=', 'cancelled')
            ->whereDate('invoice_date', '>=', $dateFrom)
            ->whereDate('invoice_date', '<=', $dateTo);

        if ($branchId && $branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        if ($saleType === 'direct_sale') {
            $query->where(fn ($q) => $q->where('invoice_type', 'direct_sale')->orWhereHas('directSale'));
        } elseif ($saleType === 'service_sale') {
            $query->where(fn ($q) => $q->where('invoice_type', 'job_card')->orWhereNotNull('job_card_id')->orWhereHas('jobCard'));
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->get();

        if ($saleType !== 'service_sale') {
            $directSalesQuery = DirectSale::with(['customer', 'branch'])
                ->whereNull('invoice_id')
                ->where('status', '!=', 'cancelled')
                ->whereDate('sale_date', '>=', $dateFrom)
                ->whereDate('sale_date', '<=', $dateTo);

            if ($branchId && $branchId !== 'all') {
                $directSalesQuery->where('branch_id', $branchId);
            }

            $unbilledDirectSales = $directSalesQuery->get();
            foreach ($unbilledDirectSales as $ds) {
                $ds->invoice_date = $ds->sale_date;
                $ds->invoice_number = $ds->bill_number;
                $ds->balance_amount = max(0, (float) $ds->grand_total - (float) $ds->amount_paid);
                $ds->is_bill = true;
                $ds->invoice_type = 'direct_sale';
                $ds->jobCard = null;
            }

            $invoices = $invoices->concat($unbilledDirectSales)->sortByDesc(fn ($item) => $item->invoice_date ?? $item->sale_date)->values();
        }

        $fileName = 'sales_report_'.now()->format('Y_m_d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        return response()->streamDownload(function () use ($invoices) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Date',
                'Invoice / Bill Number',
                'Customer / Company',
                'Vehicle',
                'Sale Type',
                'Branch',
                'Subtotal (LKR)',
                'Discount (LKR)',
                'Tax (LKR)',
                'Grand Total (LKR)',
                'Amount Paid (LKR)',
                'Balance Due (LKR)',
                'Payment Status',
            ]);

            foreach ($invoices as $inv) {
                $isDirect = ($inv->invoice_type === 'direct_sale' || ($inv->is_bill ?? false) || $inv->directSale !== null);
                $type = $isDirect ? 'Direct Sale' : 'Service Sale';
                $vehicle = $inv->jobCard?->vehicle?->license_plate ?? '-';
                $status = ($inv->balance_amount <= 0.0001 && $inv->grand_total > 0)
                    ? 'Paid in Full'
                    : ($inv->amount_paid > 0.0001 ? 'Partially Paid' : 'Unpaid');

                fputcsv($file, [
                    $inv->invoice_date?->format('Y-m-d') ?? '-',
                    $inv->invoice_number,
                    $inv->customer?->display_name ?? 'Guest Customer',
                    $vehicle,
                    $type,
                    $inv->branch?->branch_name ?? 'Main Branch',
                    number_format($inv->subtotal, 2, '.', ''),
                    number_format($inv->discount_amount, 2, '.', ''),
                    number_format($inv->tax_amount, 2, '.', ''),
                    number_format($inv->grand_total, 2, '.', ''),
                    number_format($inv->amount_paid, 2, '.', ''),
                    number_format($inv->balance_amount, 2, '.', ''),
                    $status,
                ]);
            }

            fclose($file);
        }, $fileName, $headers);
    }
}
