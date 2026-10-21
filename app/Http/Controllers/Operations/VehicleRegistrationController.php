<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\JobCard;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Services\BranchContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VehicleRegistrationController extends Controller
{
    /**
     * Display the vehicle registration & lookup screen.
     */
    public function index(Request $request): View
    {
        $vehicleTypes = VehicleType::active()->with('carpetCategories')->orderBy('name')->get();
        $initialSearch = trim((string) $request->query('search', ''));

        return view('operations.vehicles.index', compact('vehicleTypes', 'initialSearch'));
    }

    /**
     * Live search for existing vehicle by registration number (case-insensitive & normalized).
     */
    public function search(Request $request): JsonResponse
    {
        $input = trim((string) $request->query('number', ''));
        if ($input === '') {
            return response()->json([
                'exists' => false,
                'message' => 'Please enter a vehicle registration number.',
            ], 422);
        }

        $normalized = Vehicle::normalizeNumber($input);

        $vehicle = Vehicle::with(['customer.creditAccount', 'vehicleType.carpetCategories'])
            ->where(function ($q) use ($normalized, $input) {
                $q->where('vehicle_number_normalized', $normalized)
                    ->orWhereRaw("UPPER(REPLACE(REPLACE(vehicle_number, '-', ''), ' ', '')) = ?", [$normalized])
                    ->orWhere('vehicle_number', strtoupper($input));
            })
            ->first();

        if (! $vehicle) {
            return response()->json([
                'exists' => false,
                'normalized' => $normalized,
                'message' => 'New Vehicle – Enter Customer Details',
            ]);
        }

        // Active ongoing jobs (pending or in_progress)
        $ongoingJobs = JobCard::where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->with(['services', 'assignedTechnician'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'job_number' => $job->job_number,
                    'date' => $job->date ? $job->date->format('Y-m-d') : '',
                    'status' => $job->status,
                    'status_label' => ucfirst(str_replace('_', ' ', $job->status)),
                    'current_mileage' => $job->current_mileage,
                    'services' => $job->services->pluck('service_name')->toArray(),
                    'view_url' => route('operations.job-cards.show', $job),
                ];
            });

        // Completed/delivered past service history (real records only)
        $serviceHistory = JobCard::where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['completed', 'delivered'])
            ->with(['services', 'invoice'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'job_number' => $job->job_number,
                    'date' => $job->date ? $job->date->format('Y-m-d') : '',
                    'services' => $job->services->pluck('service_name')->join(', ') ?: 'General Service',
                    'mileage' => $job->current_mileage > 0 ? number_format($job->current_mileage).' km' : 'N/A',
                    'status' => ucfirst(str_replace('_', ' ', $job->status)),
                    'invoice_number' => $job->invoice?->invoice_number ?? 'N/A',
                    'view_url' => route('operations.job-cards.show', $job),
                ];
            });

        return response()->json([
            'exists' => true,
            'message' => 'Existing Vehicle Found – Customer Details Loaded',
            'vehicle' => [
                'id' => $vehicle->id,
                'vehicle_number' => $vehicle->vehicle_number,
                'vehicle_number_normalized' => $vehicle->vehicle_number_normalized,
                'vehicle_type_id' => $vehicle->vehicle_type_id,
                'vehicle_type_name' => $vehicle->vehicleType?->name,
                'make_model' => $vehicle->make_model,
                'current_mileage' => $vehicle->current_mileage,
                'next_service_mileage' => $vehicle->next_service_mileage,
                'notes' => $vehicle->notes,
                'enable_carpets' => (bool) $vehicle->vehicleType?->enable_carpets,
                'carpet_categories' => $vehicle->vehicleType?->carpetCategories->map(fn ($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                ]),
            ],
            'customer' => [
                'id' => $vehicle->customer->id,
                'customer_name' => $vehicle->customer->customer_name,
                'phone' => $vehicle->customer->phone,
                'email' => $vehicle->customer->email,
                'customer_type' => $vehicle->customer->customer_type,
                'company_name' => $vehicle->customer->company_name,
                'registration_number' => $vehicle->customer->registration_number,
                'tax_number' => $vehicle->customer->tax_number,
                'address' => $vehicle->customer->address,
                'is_company' => $vehicle->customer->isCompany(),
                'has_credit_account' => $vehicle->customer->hasCreditAccount(),
                'outstanding_credit' => $vehicle->customer->outstanding_credit,
                'available_credit' => $vehicle->customer->available_credit,
            ],
            'ongoing_jobs' => $ongoingJobs,
            'service_history' => $serviceHistory,
        ]);
    }

    /**
     * Store new vehicle or update existing, and initiate a new job card atomically.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_number' => ['required', 'string', 'max:50'],
            'customer_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
            'current_mileage' => ['nullable', 'integer', 'min:0'],
            'next_service_mileage' => ['nullable', 'integer', 'min:0'],
            'customer_type' => ['nullable', 'in:individual,company'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:500'],
            'make_model' => ['nullable', 'string', 'max:100'],
            'customer_notes' => ['nullable', 'string'],
            'ignore_active_job' => ['nullable', 'boolean'],
            'carpets' => ['nullable', 'array'],
            'carpets.*.category_id' => ['required_with:carpets', 'exists:carpet_categories,id'],
            'carpets.*.quantity' => ['required_with:carpets', 'integer', 'min:1'],
        ]);

        $normalized = Vehicle::normalizeNumber($validated['vehicle_number']);

        return DB::transaction(function () use ($validated, $normalized) {
            // Find existing vehicle or create new
            $vehicle = Vehicle::where(function ($q) use ($normalized, $validated) {
                $q->where('vehicle_number_normalized', $normalized)
                    ->orWhereRaw("UPPER(REPLACE(REPLACE(vehicle_number, '-', ''), ' ', '')) = ?", [$normalized])
                    ->orWhere('vehicle_number', strtoupper(trim($validated['vehicle_number'])));
            })->first();

            // Find or create customer
            if ($vehicle && $vehicle->customer_id) {
                $customer = Customer::find($vehicle->customer_id);
                $customerUpdate = [
                    'customer_name' => $validated['customer_name'],
                    'phone' => $validated['phone'],
                    'email' => $validated['email'] ?? $customer->email,
                    'address' => $validated['address'] ?? $customer->address,
                ];

                if ($customer->customer_type === 'company') {
                    if (! empty($validated['company_name'])) {
                        $customerUpdate['company_name'] = $validated['company_name'];
                    }
                } elseif (! empty($validated['customer_type'])) {
                    $customerUpdate['customer_type'] = $validated['customer_type'];
                    if ($validated['customer_type'] === 'company' && ! empty($validated['company_name'])) {
                        $customerUpdate['company_name'] = $validated['company_name'];
                    }
                }

                $customer->update($customerUpdate);
            } else {
                // Check if customer with this phone exists, otherwise create
                $customer = Customer::where('phone', $validated['phone'])->first();
                if ($customer) {
                    $customerUpdate = [
                        'customer_name' => $validated['customer_name'],
                        'email' => $validated['email'] ?? $customer->email,
                        'address' => $validated['address'] ?? $customer->address,
                    ];
                    if ($customer->customer_type === 'company' && ! empty($validated['company_name'])) {
                        $customerUpdate['company_name'] = $validated['company_name'];
                    } elseif (! empty($validated['customer_type'])) {
                        $customerUpdate['customer_type'] = $validated['customer_type'];
                        if ($validated['customer_type'] === 'company' && ! empty($validated['company_name'])) {
                            $customerUpdate['company_name'] = $validated['company_name'];
                        }
                    }
                    $customer->update($customerUpdate);
                } else {
                    $customer = Customer::create([
                        'customer_name' => $validated['customer_name'],
                        'phone' => $validated['phone'],
                        'email' => $validated['email'] ?? null,
                        'customer_type' => $validated['customer_type'] ?? 'individual',
                        'company_name' => ($validated['customer_type'] ?? '') === 'company' ? ($validated['company_name'] ?? null) : null,
                        'address' => $validated['address'] ?? null,
                        'status' => 'active',
                    ]);
                }
            }

            // Save or update Vehicle
            $currentMileage = ! empty($validated['current_mileage']) ? (int) $validated['current_mileage'] : ($vehicle?->current_mileage ?? 0);
            $nextMileage = ! empty($validated['next_service_mileage']) ? (int) $validated['next_service_mileage'] : ($vehicle?->next_service_mileage ?? null);

            if ($vehicle) {
                $vehicle->update([
                    'customer_id' => $customer->id,
                    'vehicle_type_id' => $validated['vehicle_type_id'],
                    'vehicle_number' => strtoupper(trim($validated['vehicle_number'])),
                    'vehicle_number_normalized' => $normalized,
                    'make_model' => $validated['make_model'] ?? $vehicle->make_model,
                    'current_mileage' => $currentMileage > 0 ? $currentMileage : $vehicle->current_mileage,
                    'next_service_mileage' => $nextMileage ?? $vehicle->next_service_mileage,
                    'notes' => $validated['customer_notes'] ?? $vehicle->notes,
                ]);
            } else {
                $vehicle = Vehicle::create([
                    'customer_id' => $customer->id,
                    'vehicle_type_id' => $validated['vehicle_type_id'],
                    'vehicle_number' => strtoupper(trim($validated['vehicle_number'])),
                    'vehicle_number_normalized' => $normalized,
                    'make_model' => $validated['make_model'] ?? null,
                    'current_mileage' => $currentMileage,
                    'next_service_mileage' => $nextMileage,
                    'notes' => $validated['customer_notes'] ?? null,
                ]);
            }

            // Guard against duplicate active jobs unless explicitly ignored
            $activeJob = JobCard::where('vehicle_id', $vehicle->id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->first();

            if ($activeJob && empty($validated['ignore_active_job'])) {
                return redirect()->route('operations.job-cards.show', $activeJob)
                    ->with('warning', "Vehicle {$vehicle->vehicle_number} already has an active Job Card (#{$activeJob->job_number}). Opened the existing job.");
            }

            $activeBranchId = BranchContext::getActiveBranchId();

            // Create new JobCard
            $jobCard = JobCard::create([
                'job_number' => JobCard::generateJobNumber(),
                'branch_id' => $activeBranchId,
                'date' => now()->toDateString(),
                'customer_id' => $customer->id,
                'vehicle_id' => $vehicle->id,
                'vehicle_type_id' => $vehicle->vehicle_type_id,
                'current_mileage' => $currentMileage,
                'next_service_mileage' => $nextMileage,
                'status' => 'pending',
                'customer_notes' => $validated['customer_notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Save Carpet Details if enabled for this vehicle type
            $selectedType = VehicleType::with('carpetCategories')->find($validated['vehicle_type_id']);
            if ($selectedType && $selectedType->enable_carpets && ! empty($validated['carpets'])) {
                $allowedCatIds = $selectedType->carpetCategories->pluck('id')->all();
                foreach ($validated['carpets'] as $carpetItem) {
                    $catId = (int) ($carpetItem['category_id'] ?? 0);
                    $qty = (int) ($carpetItem['quantity'] ?? 0);
                    if ($qty > 0 && in_array($catId, $allowedCatIds)) {
                        $cat = $selectedType->carpetCategories->firstWhere('id', $catId);
                        $jobCard->carpets()->create([
                            'carpet_category_id' => $catId,
                            'category_name' => $cat ? $cat->name : 'Carpet',
                            'quantity' => $qty,
                        ]);
                    }
                }
            }

            return redirect()->route('operations.job-cards.show', $jobCard)
                ->with('success', "Job Card #{$jobCard->job_number} created successfully for {$vehicle->vehicle_number}.");
        });
    }
}
