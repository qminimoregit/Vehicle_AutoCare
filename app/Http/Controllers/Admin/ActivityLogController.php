<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of system activity logs.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $module = $request->input('module');

        $query = ActivityLog::with('user');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($module) {
            $query->where('module', $module);
        }

        $logs = $query->latest()->paginate(15)->withQueryString();

        return view('admin.activity-logs.index', compact('logs', 'search', 'module'));
    }
}
