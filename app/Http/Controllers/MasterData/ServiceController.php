<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\StoreServiceRequest;
use App\Http\Requests\MasterData\UpdateServiceRequest;
use App\Models\ActivityLog;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Display a listing of services.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $status = $request->input('status');

        $query = Service::query();

        if ($search !== '') {
            $query->search($search);
        }

        if (in_array($status, ['active', 'inactive'])) {
            $query->where('status', $status);
        }

        $totalServices = Service::count();
        $services = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('master-data.services.index', compact('services', 'search', 'status', 'totalServices'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create(Request $request): View
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Administrator access is required to add services.');
        }

        // Suggest next service code, e.g., SRV-001, SRV-002
        $count = Service::count() + 1;
        $suggestedCode = 'SRV-'.str_pad((string) $count, 3, '0', STR_PAD_LEFT);

        return view('master-data.services.create', compact('suggestedCode'));
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $service = Service::create($request->validated());

        ActivityLog::log(
            action: 'created',
            description: "Added new service: {$service->name} ({$service->code}).",
            module: 'Services'
        );

        return redirect()->route('master-data.services.index')
            ->with('success', 'Service added successfully.');
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Request $request, Service $service): View
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Administrator access is required to edit services.');
        }

        return view('master-data.services.edit', compact('service'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
        $service->update($request->validated());

        ActivityLog::log(
            action: 'updated',
            description: "Updated service details: {$service->name} ({$service->code}).",
            module: 'Services'
        );

        return redirect()->route('master-data.services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Toggle the active/inactive status of a service.
     */
    public function toggleStatus(Request $request, Service $service): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Administrator access is required to change service status.');
        }

        $newStatus = $service->status === 'active' ? 'inactive' : 'active';
        $service->update(['status' => $newStatus]);

        ActivityLog::log(
            action: 'status_changed',
            description: "Changed service status for {$service->name} ({$service->code}) to {$newStatus}.",
            module: 'Services'
        );

        $message = $newStatus === 'active'
            ? "Service '{$service->name}' has been activated."
            : "Service '{$service->name}' has been deactivated.";

        return back()->with('success', $message);
    }

    /**
     * Prevent permanent deletion of services used in transactions.
     */
    public function destroy(Request $request, Service $service): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Administrator access is required.');
        }

        return redirect()->route('master-data.services.index')
            ->with('error', 'Services cannot be permanently deleted once created. You may deactivate the service instead.');
    }
}
