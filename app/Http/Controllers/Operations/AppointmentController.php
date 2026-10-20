<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\JobCard;
use App\Models\Service;
use App\Models\ServiceVehiclePrice;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Services\BranchContext;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    /**
     * Display Appointment Calendar and List management.
     */
    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'all');
        $search = trim((string) $request->query('search', ''));
        $activeBranchId = BranchContext::getActiveBranchId();

        $query = Appointment::with(['customer', 'vehicle.vehicleType', 'service', 'jobCard'])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'asc');

        if ($activeBranchId && ! Auth::user()->isAdmin()) {
            $query->where(function ($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId)
                    ->orWhereNull('branch_id');
            });
        }

        if ($filter === 'today') {
            $query->today();
        } elseif ($filter === 'upcoming') {
            $query->upcoming();
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('appointment_number', 'like', "%{$search}%")
                    ->orWhere('vehicle_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        }

        $appointments = $query->paginate(15)->withQueryString();

        $baseCountQuery = Appointment::query();
        if ($activeBranchId && ! Auth::user()->isAdmin()) {
            $baseCountQuery->where(function ($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId)->orWhereNull('branch_id');
            });
        }

        $counts = [
            'today' => (clone $baseCountQuery)->today()->count(),
            'upcoming' => (clone $baseCountQuery)->upcoming()->count(),
            'all' => (clone $baseCountQuery)->count(),
        ];

        $services = Service::active()->orderBy('name')->get();
        $vehicleTypes = VehicleType::active()->orderBy('name')->get();
        $branches = Branch::where('is_active', 1)->orderBy('name')->get();

        return view('appointments.index', compact(
            'appointments',
            'filter',
            'search',
            'counts',
            'services',
            'vehicleTypes',
            'branches',
            'activeBranchId'
        ));
    }

    /**
     * Provide JSON calendar events feed.
     */
    public function events(Request $request): JsonResponse
    {
        $activeBranchId = BranchContext::getActiveBranchId();
        $query = Appointment::with(['service', 'vehicle.vehicleType', 'jobCard']);

        if ($activeBranchId && ! Auth::user()->isAdmin()) {
            $query->where(function ($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId)->orWhereNull('branch_id');
            });
        }

        if ($request->has('start') && $request->has('end')) {
            $query->whereBetween('appointment_date', [
                substr($request->query('start'), 0, 10),
                substr($request->query('end'), 0, 10),
            ]);
        }

        $appointments = $query->get()->map(function ($apt) {
            $serviceName = $apt->service?->name ?? $apt->service_name ?? 'General Service';
            $timeDisplay = $apt->appointment_time;
            try {
                $parsedTime = date('h:i A', strtotime($apt->appointment_time));
                if ($parsedTime && $parsedTime !== '12:00 AM') {
                    $timeDisplay = $parsedTime;
                }
            } catch (Exception) {
                // Keep original
            }

            return [
                'id' => $apt->id,
                'appointment_number' => $apt->appointment_number,
                'title' => "{$timeDisplay} – {$apt->vehicle_number} – {$serviceName}",
                'date' => $apt->appointment_date ? $apt->appointment_date->format('Y-m-d') : '',
                'time' => $timeDisplay,
                'raw_time' => $apt->appointment_time,
                'vehicle_number' => $apt->vehicle_number,
                'customer_name' => $apt->customer_name,
                'mobile_number' => $apt->mobile_number,
                'service_name' => $serviceName,
                'service_id' => $apt->service_id,
                'vehicle_type_name' => $apt->vehicleType?->name ?? 'Vehicle',
                'vehicle_type_id' => $apt->vehicle_type_id,
                'status' => $apt->status,
                'notes' => $apt->notes,
                'job_card_id' => $apt->job_card_id,
                'job_card_number' => $apt->jobCard?->job_number,
                'job_card_url' => $apt->job_card_id ? route('operations.job-cards.show', $apt->job_card_id) : null,
            ];
        });

        return response()->json($appointments);
    }

    /**
     * Store new appointment and link/create customer and vehicle.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'mobile_number' => ['required', 'string', 'max:50'],
            'vehicle_number' => ['required', 'string', 'max:50'],
            'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
            'appointment_date' => ['required', 'date'],
            'appointment_time' => ['required', 'string', 'max:20'],
            'service_id' => ['nullable', 'exists:services,id'],
            'notes' => ['nullable', 'string', 'max:500'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'ignore_conflict' => ['nullable', 'boolean'],
        ]);

        if (! Auth::user()->isAdmin()) {
            $activeBranchId = BranchContext::getActiveBranchId();
        } else {
            $activeBranchId = $validated['branch_id'] ?? BranchContext::getActiveBranchId();
        }

        $normalizedNumber = Vehicle::normalizeNumber($validated['vehicle_number']);

        // Check for conflicts on same vehicle & date
        $conflictQuery = Appointment::where('appointment_date', $validated['appointment_date'])
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->where(function ($q) use ($normalizedNumber, $validated) {
                $q->where('vehicle_number', $validated['vehicle_number'])
                    ->orWhereHas('vehicle', function ($vq) use ($normalizedNumber) {
                        $vq->where('vehicle_number_normalized', $normalizedNumber);
                    });
            });

        if ($conflictQuery->exists() && ! $request->boolean('ignore_conflict')) {
            $conflict = $conflictQuery->first();

            return back()->withInput()->with('warning', "Scheduling conflict: Vehicle {$validated['vehicle_number']} already has Appointment #{$conflict->appointment_number} on {$validated['appointment_date']} at {$conflict->appointment_time}. Submit again to confirm.");
        }

        return DB::transaction(function () use ($validated, $normalizedNumber, $activeBranchId) {
            // Find existing vehicle or create
            $vehicle = Vehicle::where('vehicle_number_normalized', $normalizedNumber)->first();

            // Find or create customer
            if ($vehicle && $vehicle->customer_id) {
                $customer = Customer::find($vehicle->customer_id);
                if ($customer) {
                    $customer->update([
                        'customer_name' => $validated['customer_name'],
                        'phone' => $validated['mobile_number'],
                    ]);
                }
            } else {
                $customer = Customer::where('phone', $validated['mobile_number'])->first();
                if ($customer) {
                    $customer->update([
                        'customer_name' => $validated['customer_name'],
                    ]);
                } else {
                    $customer = Customer::create([
                        'customer_name' => $validated['customer_name'],
                        'phone' => $validated['mobile_number'],
                        'customer_type' => 'individual',
                        'status' => 'active',
                    ]);
                }
            }

            if (! $vehicle) {
                $vehicle = Vehicle::create([
                    'customer_id' => $customer->id,
                    'vehicle_number' => strtoupper(trim($validated['vehicle_number'])),
                    'vehicle_number_normalized' => $normalizedNumber,
                    'vehicle_type_id' => $validated['vehicle_type_id'],
                    'status' => 'active',
                ]);
            } else {
                // Ensure customer and vehicle type are synced
                $vehicle->update([
                    'customer_id' => $customer->id,
                    'vehicle_type_id' => $validated['vehicle_type_id'] ?? $vehicle->vehicle_type_id,
                ]);
            }

            $service = ! empty($validated['service_id']) ? Service::find($validated['service_id']) : null;

            $appointment = Appointment::create([
                'appointment_number' => Appointment::generateAppointmentNumber(),
                'branch_id' => $activeBranchId,
                'customer_id' => $customer->id,
                'vehicle_id' => $vehicle->id,
                'vehicle_type_id' => $validated['vehicle_type_id'],
                'service_id' => $service?->id,
                'customer_name' => $validated['customer_name'],
                'mobile_number' => $validated['mobile_number'],
                'vehicle_number' => $vehicle->vehicle_number,
                'service_name' => $service?->name,
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_time'],
                'status' => 'scheduled',
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('appointments.index')
                ->with('success', "Appointment #{$appointment->appointment_number} created successfully for {$appointment->vehicle_number}.");
        });
    }

    /**
     * Update appointment details.
     */
    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'appointment_date' => ['required', 'date'],
            'appointment_time' => ['required', 'string', 'max:20'],
            'service_id' => ['nullable', 'exists:services,id'],
            'status' => ['required', 'in:scheduled,confirmed,arrived,in_service,completed,cancelled,no_show'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $service = ! empty($validated['service_id']) ? Service::find($validated['service_id']) : null;

        $appointment->update([
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'service_id' => $service?->id,
            'service_name' => $service?->name ?? $appointment->service_name,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $appointment->notes,
        ]);

        return back()->with('success', "Appointment #{$appointment->appointment_number} updated successfully.");
    }

    /**
     * Mark customer arrived.
     */
    public function markArrived(Appointment $appointment): RedirectResponse
    {
        if ($appointment->status === 'cancelled') {
            return back()->with('error', 'Cannot mark a cancelled appointment as arrived.');
        }

        $appointment->update(['status' => 'arrived']);

        return back()->with('success', "Customer arrived for Appointment #{$appointment->appointment_number}. Ready to create Job Card.");
    }

    /**
     * Convert an Appointment into a Job Card seamlessly.
     */
    public function createJobCard(Appointment $appointment): RedirectResponse
    {
        // If Job Card already exists, redirect to it
        if ($appointment->job_card_id && $appointment->jobCard) {
            return redirect()->route('operations.job-cards.show', $appointment->jobCard)
                ->with('info', "Job Card #{$appointment->jobCard->job_number} is already linked to this appointment.");
        }

        return DB::transaction(function () use ($appointment) {
            $branchId = $appointment->branch_id ?? BranchContext::getActiveBranchId();

            $jobCard = JobCard::create([
                'job_number' => JobCard::generateJobNumber(),
                'branch_id' => $branchId,
                'customer_id' => $appointment->customer_id,
                'vehicle_id' => $appointment->vehicle_id,
                'vehicle_type_id' => $appointment->vehicle_type_id,
                'appointment_id' => $appointment->id,
                'date' => now()->toDateString(),
                'current_mileage' => $appointment->vehicle?->current_mileage ?? 0,
                'next_service_mileage' => $appointment->vehicle?->next_service_mileage ?? 0,
                'service_notes' => "Appointment #{$appointment->appointment_number}".($appointment->notes ? " - {$appointment->notes}" : ''),
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            // Add selected service if present
            if ($appointment->service_id && $appointment->service) {
                $service = $appointment->service;
                $pricing = ServiceVehiclePrice::where('service_id', $service->id)
                    ->where('vehicle_type_id', $jobCard->vehicle_type_id)
                    ->first();

                $price = ($pricing && $pricing->service_price !== null) ? (float) $pricing->service_price : 0.0;

                $jobCard->services()->create([
                    'service_id' => $service->id,
                    'service_code' => $service->code,
                    'service_name' => $service->name,
                    'unit_price' => $price,
                    'quantity' => 1.0,
                    'line_total' => $price,
                    'is_price_custom' => false,
                ]);

                $jobCard->recalculateTotals();
            }

            // Update appointment
            $appointment->update([
                'job_card_id' => $jobCard->id,
                'status' => 'in_service',
            ]);

            return redirect()->route('operations.job-cards.show', $jobCard)
                ->with('success', "Job Card #{$jobCard->job_number} created successfully from Appointment #{$appointment->appointment_number}.");
        });
    }

    /**
     * Cancel an appointment.
     */
    public function cancel(Request $request, Appointment $appointment): RedirectResponse
    {
        if ($appointment->status === 'cancelled') {
            return back()->with('error', 'Appointment is already cancelled.');
        }

        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', "Appointment #{$appointment->appointment_number} cancelled.");
    }
}
