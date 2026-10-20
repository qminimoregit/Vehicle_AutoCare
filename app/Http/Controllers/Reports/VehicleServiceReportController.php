<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VehicleServiceReportController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $vehicleId = $request->input('vehicle_id');

        $selectedVehicle = null;
        $serviceHistory = collect();
        $summary = [
            'total_services' => 0,
            'latest_service_date' => null,
            'latest_mileage' => null,
            'next_service_mileage' => null,
        ];

        if ($vehicleId) {
            $selectedVehicle = Vehicle::with(['customer', 'vehicleType'])->find($vehicleId);
        } elseif ($search !== '') {
            $normalized = Vehicle::normalizeNumber($search);
            $selectedVehicle = Vehicle::with(['customer', 'vehicleType'])
                ->where('vehicle_number_normalized', 'like', "%{$normalized}%")
                ->orWhere('vehicle_number', 'like', "%{$search}%")
                ->orWhereHas('customer', function ($q) use ($search) {
                    $q->where('customer_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })
                ->first();
        }

        if ($selectedVehicle) {
            $jobQuery = $selectedVehicle->jobCards()
                ->with(['services.service', 'products.product', 'invoice'])
                ->where('status', '!=', 'cancelled');

            if ($dateFrom) {
                $jobQuery->whereDate('date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $jobQuery->whereDate('date', '<=', $dateTo);
            }

            $serviceHistory = $jobQuery->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            $latestJob = $serviceHistory->first();

            $summary = [
                'total_services' => $serviceHistory->count(),
                'latest_service_date' => $latestJob ? ($latestJob->date ? Carbon::parse($latestJob->date)->format('Y-m-d') : null) : null,
                'latest_mileage' => $latestJob ? $latestJob->current_mileage : $selectedVehicle->current_mileage,
                'next_service_mileage' => $latestJob ? ($latestJob->next_service_mileage ?: $selectedVehicle->next_service_mileage) : $selectedVehicle->next_service_mileage,
            ];
        }

        return view('reports.vehicle-service.index', compact(
            'search',
            'dateFrom',
            'dateTo',
            'selectedVehicle',
            'serviceHistory',
            'summary'
        ));
    }

    /**
     * Search vehicle suggestions for autocomplete.
     */
    public function search(Request $request): JsonResponse
    {
        $term = trim((string) $request->input('term'));
        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $normalized = Vehicle::normalizeNumber($term);

        $vehicles = Vehicle::with('customer')
            ->where('vehicle_number_normalized', 'like', "%{$normalized}%")
            ->orWhere('vehicle_number', 'like', "%{$term}%")
            ->orWhereHas('customer', function ($q) use ($term) {
                $q->where('customer_name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($v) {
                return [
                    'id' => $v->id,
                    'vehicle_number' => $v->vehicle_number,
                    'make_model' => $v->make_model ?? 'Vehicle',
                    'customer_name' => $v->customer ? $v->customer->customer_name : 'No Customer',
                    'phone' => $v->customer ? $v->customer->phone : '',
                ];
            });

        return response()->json($vehicles);
    }

    public function print(Request $request, Vehicle $vehicle): View
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $vehicle->loadMissing(['customer', 'vehicleType']);

        $jobQuery = $vehicle->jobCards()
            ->with(['services.service', 'products.product', 'invoice'])
            ->where('status', '!=', 'cancelled');

        if ($dateFrom) {
            $jobQuery->whereDate('date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $jobQuery->whereDate('date', '<=', $dateTo);
        }

        $serviceHistory = $jobQuery->orderBy('date', 'asc')->get();

        $latestJob = $serviceHistory->last();
        $summary = [
            'total_services' => $serviceHistory->count(),
            'latest_service_date' => $latestJob ? ($latestJob->date ? Carbon::parse($latestJob->date)->format('Y-m-d') : null) : null,
            'latest_mileage' => $latestJob ? $latestJob->current_mileage : $vehicle->current_mileage,
            'next_service_mileage' => $latestJob ? ($latestJob->next_service_mileage ?: $vehicle->next_service_mileage) : $vehicle->next_service_mileage,
        ];

        return view('reports.vehicle-service.print', compact(
            'vehicle',
            'serviceHistory',
            'summary',
            'dateFrom',
            'dateTo'
        ));
    }

    public function export(Request $request, Vehicle $vehicle): StreamedResponse
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $vehicle->loadMissing(['customer', 'vehicleType']);

        $jobQuery = $vehicle->jobCards()
            ->with(['services.service', 'products.product', 'invoice'])
            ->where('status', '!=', 'cancelled');

        if ($dateFrom) {
            $jobQuery->whereDate('date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $jobQuery->whereDate('date', '<=', $dateTo);
        }

        $serviceHistory = $jobQuery->orderBy('date', 'desc')->get();
        $fileName = 'vehicle_service_history_'.Vehicle::normalizeNumber($vehicle->vehicle_number).'_'.now()->format('Ymd').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        return response()->streamDownload(function () use ($vehicle, $serviceHistory) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['VEHICLE SERVICE RECORD', "Vehicle: {$vehicle->vehicle_number}", "Customer: {$vehicle->customer?->customer_name}"]);
            fputcsv($file, []);

            fputcsv($file, [
                'Date',
                'Job Number',
                'Recorded Mileage (km)',
                'Next Service Due (km)',
                'Services Performed',
                'Products & Parts Used',
                'Invoice Number',
                'Total Amount (LKR)',
                'Status',
            ]);

            foreach ($serviceHistory as $job) {
                $servicesList = $job->services->map(fn ($s) => $s->service?->service_name ?? 'Service')->implode('; ');
                $productsList = $job->products->map(fn ($p) => ($p->product_name ?? 'Item').' (Qty: '.$p->quantity.')')->implode('; ');

                fputcsv($file, [
                    $job->date?->format('Y-m-d') ?? '-',
                    $job->job_number,
                    $job->current_mileage ? number_format($job->current_mileage) : '-',
                    $job->next_service_mileage ? number_format($job->next_service_mileage) : '-',
                    $servicesList,
                    $productsList,
                    $job->invoice?->invoice_number ?? '-',
                    number_format($job->grand_total, 2, '.', ''),
                    ucfirst($job->status),
                ]);
            }

            fclose($file);
        }, $fileName, $headers);
    }
}
