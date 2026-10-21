<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\StoreVehicleTypeRequest;
use App\Http\Requests\MasterData\UpdateVehicleTypeRequest;
use App\Models\ActivityLog;
use App\Models\CarpetCategory;
use App\Models\VehicleType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        $query = VehicleType::with('carpetCategories');

        if ($search !== '') {
            $query->search($search);
        }

        if (in_array($status, ['active', 'inactive'])) {
            $query->where('status', $status);
        }

        $totalVehicleTypes = VehicleType::count();
        $vehicleTypes = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('master-data.vehicle-types.index', compact('vehicleTypes', 'search', 'status', 'totalVehicleTypes'));
    }

    /**
     * Show the form for creating a new vehicle type.
     */
    public function create(Request $request): View
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Administrator access is required to add vehicle types.');
        }

        $availableCarpetCategories = CarpetCategory::active()->orderBy('name')->get();

        return view('master-data.vehicle-types.create', compact('availableCarpetCategories'));
    }

    /**
     * Store a newly created vehicle type in storage.
     */
    public function store(StoreVehicleTypeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $enableCarpets = $request->boolean('enable_carpets');
        $data['enable_carpets'] = $enableCarpets;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('vehicle-types', 'public');
        }

        $vehicleType = VehicleType::create($data);

        // Sync carpet categories if carpets are enabled
        if ($enableCarpets) {
            $categoryIds = $request->input('carpet_categories', []);
            if (! empty($data['new_carpet_category'])) {
                $newCat = CarpetCategory::firstOrCreate([
                    'name' => trim($data['new_carpet_category']),
                ]);
                $categoryIds[] = $newCat->id;
            }
            $vehicleType->carpetCategories()->sync(array_unique($categoryIds));
        } else {
            $vehicleType->carpetCategories()->detach();
        }

        ActivityLog::log(
            action: 'created',
            description: "Added new vehicle type: {$vehicleType->name} (Carpets: ".($enableCarpets ? 'Enabled' : 'Disabled').').',
            module: 'Vehicle Types'
        );

        return redirect()->route('master-data.vehicle-types.index')
            ->with('success', 'Vehicle type added successfully.');
    }

    /**
     * Show the form for editing the specified vehicle type.
     */
    public function edit(Request $request, VehicleType $vehicleType): View
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Administrator access is required to edit vehicle types.');
        }

        $vehicleType->load('carpetCategories');
        $availableCarpetCategories = CarpetCategory::active()->orderBy('name')->get();

        return view('master-data.vehicle-types.edit', compact('vehicleType', 'availableCarpetCategories'));
    }

    /**
     * Update the specified vehicle type in storage.
     */
    public function update(UpdateVehicleTypeRequest $request, VehicleType $vehicleType): RedirectResponse
    {
        $data = $request->validated();
        $enableCarpets = $request->boolean('enable_carpets');
        $data['enable_carpets'] = $enableCarpets;

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($vehicleType->image && Storage::disk('public')->exists($vehicleType->image)) {
                Storage::disk('public')->delete($vehicleType->image);
            }

            $data['image'] = $request->file('image')->store('vehicle-types', 'public');
        } else {
            // Keep existing image if not provided
            unset($data['image']);
        }

        $vehicleType->update($data);

        // Sync carpet categories if carpets are enabled
        if ($enableCarpets) {
            $categoryIds = $request->input('carpet_categories', []);
            if (! empty($data['new_carpet_category'])) {
                $newCat = CarpetCategory::firstOrCreate([
                    'name' => trim($data['new_carpet_category']),
                ]);
                $categoryIds[] = $newCat->id;
            }
            $vehicleType->carpetCategories()->sync(array_unique($categoryIds));
        } else {
            $vehicleType->carpetCategories()->detach();
        }

        ActivityLog::log(
            action: 'updated',
            description: "Updated vehicle type: {$vehicleType->name} (Carpets: ".($enableCarpets ? 'Enabled' : 'Disabled').').',
            module: 'Vehicle Types'
        );

        return redirect()->route('master-data.vehicle-types.index')
            ->with('success', 'Vehicle type updated successfully.');
    }

    /**
     * Add a new carpet category dynamically (AJAX).
     */
    public function storeCarpetCategory(Request $request): JsonResponse
    {
        if (! $request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $name = trim($validated['name']);
        $category = CarpetCategory::firstOrCreate(
            ['name' => $name],
            ['status' => 'active']
        );

        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
            ],
            'message' => "Carpet category '{$category->name}' is ready.",
        ]);
    }

    /**
     * Remove a carpet category (AJAX).
     */
    public function destroyCarpetCategory(Request $request, CarpetCategory $carpetCategory): JsonResponse
    {
        if (! $request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $carpetCategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Carpet category removed.',
        ]);
    }

    /**
     * Toggle the active/inactive status of a vehicle type.
     */
    public function toggleStatus(Request $request, VehicleType $vehicleType): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Administrator access is required to change vehicle type status.');
        }

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

    /**
     * Prevent permanent deletion of vehicle types.
     */
    public function destroy(Request $request, VehicleType $vehicleType): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Administrator access is required.');
        }

        return redirect()->route('master-data.vehicle-types.index')
            ->with('error', 'Vehicle types cannot be permanently deleted once created. You may deactivate the vehicle type instead.');
    }
}
