<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\UpdateServiceVehiclePricesRequest;
use App\Models\ActivityLog;
use App\Models\Service;
use App\Models\ServiceVehiclePrice;
use App\Models\VehicleType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ServicePaymentController extends Controller
{
    /**
     * Display a listing of services with their configured price counts.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $query = Service::query()->active();

        if ($search !== '') {
            $query->search($search);
        }

        $totalVehicleTypes = VehicleType::active()->count();

        $services = $query
            ->withCount(['vehiclePrices as configured_count' => function ($q) {
                $q->whereNotNull('service_price');
            }])
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('master-data.service-payments.index', compact('services', 'search', 'totalVehicleTypes'));
    }

    /**
     * Show the price and cost management page for a specific service.
     */
    public function edit(Request $request, Service $service): View
    {
        $search = trim((string) $request->input('search'));

        $vehicleTypesQuery = VehicleType::query()->active();

        if ($search !== '') {
            $vehicleTypesQuery->search($search);
        }

        $vehicleTypes = $vehicleTypesQuery->orderBy('name', 'asc')->paginate(10)->withQueryString();

        // Load existing price mappings for this service
        $existingPrices = ServiceVehiclePrice::where('service_id', $service->id)
            ->get()
            ->keyBy('vehicle_type_id');

        $canEdit = $request->user() && $request->user()->isAdmin();

        return view('master-data.service-payments.edit', compact(
            'service',
            'vehicleTypes',
            'existingPrices',
            'search',
            'canEdit'
        ));
    }

    /**
     * Update service prices and costs for vehicle types.
     */
    public function update(UpdateServiceVehiclePricesRequest $request, Service $service): RedirectResponse
    {
        $submittedPrices = $request->input('prices', []);

        DB::transaction(function () use ($service, $submittedPrices) {
            foreach ($submittedPrices as $vehicleTypeId => $data) {
                // Ensure vehicle type exists
                $vehicleType = VehicleType::find($vehicleTypeId);
                if (! $vehicleType) {
                    continue;
                }

                $priceInput = isset($data['price']) && $data['price'] !== '' ? $data['price'] : null;
                $costInput = isset($data['cost']) && $data['cost'] !== '' ? $data['cost'] : null;

                // Format decimals if values are present
                $price = $priceInput !== null ? round((float) $priceInput, 2) : null;
                $cost = $costInput !== null ? round((float) $costInput, 2) : null;

                // Update or create record
                if ($price !== null || $cost !== null) {
                    ServiceVehiclePrice::updateOrCreate(
                        [
                            'service_id' => $service->id,
                            'vehicle_type_id' => $vehicleTypeId,
                        ],
                        [
                            'service_price' => $price,
                            'service_cost' => $cost,
                        ]
                    );
                } else {
                    // If previously saved and now cleared, update to null
                    $existing = ServiceVehiclePrice::where('service_id', $service->id)
                        ->where('vehicle_type_id', $vehicleTypeId)
                        ->first();

                    if ($existing) {
                        $existing->update([
                            'service_price' => null,
                            'service_cost' => null,
                        ]);
                    }
                }
            }
        });

        ActivityLog::log(
            action: 'updated',
            description: "Updated service prices and costs for: {$service->name} ({$service->code}).",
            module: 'Service Payments'
        );

        return redirect()->route('master-data.service-payments.edit', $service)
            ->with('success', 'Service prices and costs updated successfully.');
    }
}
