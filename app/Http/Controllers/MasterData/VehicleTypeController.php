<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\VehicleType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VehicleTypeController extends Controller
{
    /**
     * Display a listing of vehicle types.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $status = $request->input('status');

        $query = VehicleType::query();

        if ($search !== '') {
            $query->search($search);
        }

        if (in_array($status, ['active', 'inactive'])) {
            $query->where('status', $status);
        }

        $vehicleTypes = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('master-data.vehicle-types.index', compact('vehicleTypes', 'search', 'status'));
    }

    /**
     * Show the form for creating a new vehicle type.
     */
    public function create(): View
    {
        return view('master-data.vehicle-types.create');
    }

    /**
     * Store a newly created vehicle type in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:vehicle_types,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'name.required' => 'Vehicle type name is required.',
            'name.unique' => 'This vehicle type name already exists.',
        ]);

        $vehicleType = VehicleType::create($validated);

        ActivityLog::log(
            action: 'created',
            description: "Added new vehicle type: {$vehicleType->name}.",
            module: 'Vehicle Types'
        );

        return redirect()->route('master-data.vehicle-types.index')
            ->with('success', "Vehicle type '{$vehicleType->name}' added successfully.");
    }

    /**
     * Show the form for editing the specified vehicle type.
     */
    public function edit(VehicleType $vehicleType): View
    {
        return view('master-data.vehicle-types.edit', compact('vehicleType'));
    }

    /**
     * Update the specified vehicle type in storage.
     */
    public function update(Request $request, VehicleType $vehicleType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('vehicle_types', 'name')->ignore($vehicleType->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'name.required' => 'Vehicle type name is required.',
            'name.unique' => 'This vehicle type name is already in use.',
        ]);

        $vehicleType->update($validated);

        ActivityLog::log(
            action: 'updated',
            description: "Updated vehicle type: {$vehicleType->name}.",
            module: 'Vehicle Types'
        );

        return redirect()->route('master-data.vehicle-types.index')
            ->with('success', "Vehicle type '{$vehicleType->name}' updated successfully.");
    }

    /**
     * Toggle the active/inactive status of a vehicle type.
     */
    public function toggleStatus(VehicleType $vehicleType): RedirectResponse
    {
        $newStatus = $vehicleType->status === 'active' ? 'inactive' : 'active';
        $vehicleType->update(['status' => $newStatus]);

        ActivityLog::log(
            action: 'status_changed',
            description: "Changed vehicle type status for {$vehicleType->name} to {$newStatus}.",
            module: 'Vehicle Types'
        );

        $message = $newStatus === 'active'
            ? "Vehicle type '{$vehicleType->name}' has been activated."
            : "Vehicle type '{$vehicleType->name}' has been deactivated.";

        return back()->with('success', $message);
    }
}
