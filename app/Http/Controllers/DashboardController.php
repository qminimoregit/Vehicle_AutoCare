<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Service;
use App\Models\VehicleType;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the main system dashboard.
     */
    public function index(): View
    {
        // Real counts for available Master Data
        $totalServices = Service::count();
        $activeServices = Service::active()->count();
        $totalVehicleTypes = VehicleType::count();
        $activeVehicleTypes = VehicleType::active()->count();

        // Business metrics for future modules (using real 0 values, never fake demo numbers)
        $todayJobs = 0;
        $pendingJobs = 0;
        $completedJobs = 0;
        $todaySales = 0.00;
        $pendingPayments = 0.00;

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
