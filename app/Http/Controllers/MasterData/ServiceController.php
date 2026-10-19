<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

        $services = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('master-data.services.index', compact('services', 'search', 'status'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create(): View
    {
        // Suggest next service code, e.g., SRV-001, SRV-002
        $count = Service::count() + 1;
        $suggestedCode = 'SRV-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);

        return view('master-data.services.create', compact('suggestedCode'));
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:services,code'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'code.required' => 'Service code is required.',
            'code.unique' => 'This service code has already been registered.',
            'name.required' => 'Service name is required.',
        ]);

        $service = Service::create($validated);

        ActivityLog::log(
            action: 'created',
            description: "Added new service: {$service->name} ({$service->code}).",
            module: 'Services'
        );

        return redirect()->route('master-data.services.index')
            ->with('success', "Service '{$service->name}' added successfully.");
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service): View
    {
        return view('master-data.services.edit', compact('service'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('services', 'code')->ignore($service->id)],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'code.required' => 'Service code is required.',
            'code.unique' => 'This service code is already in use by another service.',
            'name.required' => 'Service name is required.',
        ]);

        $service->update($validated);

        ActivityLog::log(
            action: 'updated',
            description: "Updated service details: {$service->name} ({$service->code}).",
            module: 'Services'
        );

        return redirect()->route('master-data.services.index')
            ->with('success', "Service '{$service->name}' updated successfully.");
    }

    /**
     * Toggle the active/inactive status of a service.
     */
    public function toggleStatus(Service $service): RedirectResponse
    {
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
}
