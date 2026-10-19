<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServicePaymentController extends Controller
{
    /**
     * Display the Manage Service Payment page shell.
     * Calculation formulas are pending confirmation as per system specification.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $vehicleTypeId = $request->input('vehicle_type_id');

        $servicesQuery = Service::query()->active();

        if ($search !== '') {
            $servicesQuery->search($search);
        }

        $services = $servicesQuery->orderBy('name', 'asc')->paginate(10)->withQueryString();
        $vehicleTypes = VehicleType::active()->orderBy('name', 'asc')->get();

        return view('master-data.service-payments.index', compact('services', 'vehicleTypes', 'search', 'vehicleTypeId'));
    }
}
