<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\JobCard;
use App\Models\Service;
use App\Models\VehicleType;
use App\Services\BranchContext;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the main system dashboard with real database records and branch scoping.
     */
    public function index(): View
    {
        // Real counts for Master Data
        $totalServices = Service::count();
        $activeServices = Service::active()->count();
        $totalVehicleTypes = VehicleType::count();
        $activeVehicleTypes = VehicleType::active()->count();

        $activeBranchId = BranchContext::getActiveBranchId();
        $today = Carbon::today('Asia/Colombo')->toDateString();

        // 1. Today's Jobs: Count jobs created today
        $todayJobsQuery = JobCard::where(function ($q) use ($today) {
            $q->whereDate('date', $today)
                ->orWhereDate('created_at', $today);
        });
        if ($activeBranchId) {
            $todayJobsQuery->where('branch_id', $activeBranchId);
        }
        $todayJobs = $todayJobsQuery->count();

        // 2. Pending Jobs: Count jobs currently awaiting work
        $pendingJobsQuery = JobCard::whereIn('status', ['pending', 'in_progress', 'additional_work']);
        if ($activeBranchId) {
            $pendingJobsQuery->where('branch_id', $activeBranchId);
        }
        $pendingJobs = $pendingJobsQuery->count();

        // 3. Completed Jobs: Completed jobs for today
        $completedJobsQuery = JobCard::whereIn('status', ['completed', 'delivered'])
            ->where(function ($q) use ($today) {
                $q->whereDate('completed_at', $today)
                    ->orWhere(function ($sub) use ($today) {
                        $sub->whereNull('completed_at')
                            ->whereDate('updated_at', $today);
                    });
            });
        if ($activeBranchId) {
            $completedJobsQuery->where('branch_id', $activeBranchId);
        }
        $completedJobs = $completedJobsQuery->count();

        // 4. Today's Sales: Recognized sales from non-cancelled invoices posted today
        // (Includes Job Cards, Direct Sales, and converted Quotations without double-counting)
        $todaySalesQuery = Invoice::where('status', '!=', 'cancelled')
            ->whereDate('invoice_date', $today);
        if ($activeBranchId) {
            $todaySalesQuery->where(function ($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId)
                    ->orWhereNull('branch_id');
            });
        }
        $todaySales = (float) $todaySalesQuery->sum('grand_total');

        // 5. Pending Payments: Valid unpaid customer invoice balances
        $pendingPaymentsQuery = Invoice::where('status', '!=', 'cancelled')
            ->where('balance_amount', '>', 0);
        if ($activeBranchId) {
            $pendingPaymentsQuery->where(function ($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId)
                    ->orWhereNull('branch_id');
            });
        }
        $pendingPayments = (float) $pendingPaymentsQuery->sum('balance_amount');

        // Recent audit/activity logs
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard.index', compact(
            'totalServices',
            'activeServices',
            'totalVehicleTypes',
            'activeVehicleTypes',
            'todayJobs',
            'pendingJobs',
            'completedJobs',
            'todaySales',
            'pendingPayments',
            'recentActivities'
        ));
    }
}
