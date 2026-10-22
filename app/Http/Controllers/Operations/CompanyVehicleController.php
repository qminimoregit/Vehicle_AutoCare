<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\CustomerCreditAccount;
use App\Models\Invoice;
use App\Models\JobCard;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Services\BranchContext;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CompanyVehicleController extends Controller
{
    /**
     * Display Company & Vehicles unified management screen.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));
        $selectedId = $request->input('selected_id');

        $query = Customer::where('customer_type', 'company')
            ->withCount('vehicles')
            ->with(['creditAccount', 'vehicles.vehicleType', 'vehicles.lastCompletedJob']);

        if ($search !== '') {
            $normalizedSearch = Vehicle::normalizeNumber($search);
            $query->where(function ($q) use ($search, $normalizedSearch) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('vehicles', function ($vq) use ($search, $normalizedSearch) {
                        $vq->where('vehicle_number', 'like', "%{$search}%")
                            ->orWhere('driver_name', 'like', "%{$search}%")
                            ->orWhere('driver_mobile', 'like', "%{$search}%");
                        if ($normalizedSearch !== '') {
                            $vq->orWhere('vehicle_number_normalized', 'like', "%{$normalizedSearch}%");
                        }
                    });
            });
        }

        $companies = $query->orderBy('company_name', 'asc')->get();

        // Selected Company resolution
        $selectedCompany = null;
        if ($selectedId) {
            $selectedCompany = $companies->firstWhere('id', (int) $selectedId)
                ?? Customer::where('customer_type', 'company')
                    ->with(['creditAccount', 'vehicles.vehicleType', 'vehicles.lastCompletedJob.services'])
                    ->find((int) $selectedId);
        }

        if (! $selectedCompany && $companies->isNotEmpty()) {
            $selectedCompany = $companies->first();
        }

        if ($selectedCompany && ! $selectedCompany->relationLoaded('vehicles')) {
            $selectedCompany->load(['creditAccount', 'vehicles.vehicleType', 'vehicles.lastCompletedJob.services']);
        }

        $vehicleTypes = VehicleType::active()->orderBy('name')->get();

        return view('operations.company-vehicles.index', compact(
            'companies',
            'selectedCompany',
            'search',
            'vehicleTypes'
        ));
    }

    /**
     * Register a new company customer with credit terms.
     */
    public function storeCompany(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('view_employees')) {
            // All management staff with customer/job permissions can view, admin can manage credit limit
        }

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:150'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'customer_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:500'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'payment_terms' => ['required', 'in:immediate,7_days,15_days,30_days,custom'],
            'custom_days' => ['nullable', 'required_if:payment_terms,custom', 'integer', 'min:1', 'max:365'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $companyName = trim($validated['company_name']);
        $regNumber = ! empty($validated['registration_number']) ? trim($validated['registration_number']) : null;
        $phone = trim($validated['phone']);

        // Prevent duplicate company registration
        $existing = Customer::where('customer_type', 'company')
            ->where(function ($q) use ($companyName, $regNumber) {
                $q->whereRaw('LOWER(company_name) = ?', [strtolower($companyName)]);
                if ($regNumber) {
                    $q->orWhere('registration_number', $regNumber);
                }
            })
            ->first();

        if ($existing) {
            return back()->withInput()->with('error', "Company '{$existing->company_name}' is already registered in the system (Contact: {$existing->customer_name}, Phone: {$existing->phone}).");
        }

        $termsDays = match ($validated['payment_terms']) {
            'immediate' => 0,
            '7_days' => 7,
            '15_days' => 15,
            '30_days' => 30,
            'custom' => (int) ($validated['custom_days'] ?? 30),
        };

        $creditLimit = ! empty($validated['credit_limit']) ? (float) $validated['credit_limit'] : 0.00;

        $company = DB::transaction(function () use ($validated, $companyName, $regNumber, $phone, $termsDays, $creditLimit, $user) {
            $customer = Customer::create([
                'customer_type' => 'company',
                'company_name' => $companyName,
                'registration_number' => $regNumber,
                'customer_name' => trim($validated['customer_name']),
                'phone' => $phone,
                'email' => ! empty($validated['email']) ? trim($validated['email']) : null,
                'address' => $validated['address'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'],
            ]);

            // Create linked Credit Account
            CustomerCreditAccount::create([
                'customer_id' => $customer->id,
                'credit_limit' => $creditLimit,
                'payment_terms_days' => $termsDays,
                'opening_balance' => 0.00,
                'status' => $validated['status'] === 'active' ? 'active' : 'suspended',
                'notes' => "Account opened on registration. Payment terms: {$validated['payment_terms']}.",
                'created_by' => $user->id,
            ]);

            ActivityLog::log(
                action: 'created_company',
                description: "Registered company customer '{$customer->company_name}' with credit limit LKR ".number_format($creditLimit, 2)." and {$termsDays}-day terms.",
                module: 'Company & Vehicles',
                userId: $user->id
            );

            return $customer;
        });

        return redirect()->route('operations.company-vehicles.index', ['selected_id' => $company->id])
            ->with('success', "Company '{$company->company_name}' registered successfully. You can now add vehicles.");
    }

    /**
     * Update an existing company customer's details and credit terms.
     */
    public function updateCompany(Request $request, Customer $customer): RedirectResponse|JsonResponse
    {
        $user = Auth::user();

        if ($customer->customer_type !== 'company') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Customer is not a company.'], 404);
            }
            abort(404, 'Customer is not a company.');
        }

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:150'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'customer_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:500'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'payment_terms' => ['required', 'in:immediate,7_days,15_days,30_days,custom'],
            'custom_days' => ['nullable', 'required_if:payment_terms,custom', 'integer', 'min:1', 'max:365'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $companyName = trim($validated['company_name']);
        $regNumber = ! empty($validated['registration_number']) ? trim($validated['registration_number']) : null;
        $phone = trim($validated['phone']);

        // Check duplicate name or reg number on other companies
        $duplicate = Customer::where('customer_type', 'company')
            ->where('id', '!=', $customer->id)
            ->where(function ($q) use ($companyName, $regNumber) {
                $q->whereRaw('LOWER(company_name) = ?', [strtolower($companyName)]);
                if ($regNumber) {
                    $q->orWhere('registration_number', $regNumber);
                }
            })
            ->first();

        if ($duplicate) {
            $msg = "Another company '{$duplicate->company_name}' already exists with this registration number or name.";
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $msg,
                    'errors' => ['company_name' => [$msg]],
                ], 422);
            }
            return back()->withInput()->with('error', $msg);
        }

        $termsDays = match ($validated['payment_terms']) {
            'immediate' => 0,
            '7_days' => 7,
            '15_days' => 15,
            '30_days' => 30,
            'custom' => (int) ($validated['custom_days'] ?? 30),
        };

        $creditLimit = ! empty($validated['credit_limit']) ? (float) $validated['credit_limit'] : 0.00;

        DB::transaction(function () use ($customer, $validated, $companyName, $regNumber, $phone, $termsDays, $creditLimit, $user) {
            $customer->update([
                'company_name' => $companyName,
                'registration_number' => $regNumber,
                'customer_name' => trim($validated['customer_name']),
                'phone' => $phone,
                'email' => ! empty($validated['email']) ? trim($validated['email']) : null,
                'address' => $validated['address'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'],
            ]);

            $creditAccount = $customer->creditAccount;
            if ($creditAccount) {
                // Only admin can adjust credit limit if restricted
                $updateData = [
                    'payment_terms_days' => $termsDays,
                    'status' => $validated['status'] === 'active' ? 'active' : 'suspended',
                ];
                if ($user->isAdmin() || $creditAccount->credit_limit != $creditLimit) {
                    $updateData['credit_limit'] = $creditLimit;
                }
                $creditAccount->update($updateData);
            } else {
                CustomerCreditAccount::create([
                    'customer_id' => $customer->id,
                    'credit_limit' => $creditLimit,
                    'payment_terms_days' => $termsDays,
                    'opening_balance' => 0.00,
                    'status' => $validated['status'] === 'active' ? 'active' : 'suspended',
                    'notes' => 'Created during company edit.',
                    'created_by' => $user->id,
                ]);
            }

            ActivityLog::log(
                action: 'updated_company',
                description: "Updated company profile '{$customer->company_name}'.",
                module: 'Company & Vehicles',
                userId: $user->id
            );
        });

        $successMessage = "Company '{$customer->company_name}' updated successfully.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'company' => $customer->fresh(['creditAccount']),
            ]);
        }

        return redirect()->route('operations.company-vehicles.index', ['selected_id' => $customer->id])
            ->with('success', $successMessage);
    }

    /**
     * Register or link a vehicle to a company.
     */
    public function storeVehicle(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'company_id' => ['required', 'exists:customers,id'],
            'vehicle_number' => ['required', 'string', 'max:50'],
            'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'year_of_manufacture' => ['nullable', 'integer', 'min:1960', 'max:'.(date('Y') + 1)],
            'driver_name' => ['nullable', 'string', 'max:150'],
            'driver_mobile' => ['nullable', 'string', 'max:50'],
            'current_mileage' => ['nullable', 'integer', 'min:0'],
            'next_service_mileage' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'transfer_confirmed' => ['nullable', 'boolean'],
        ]);

        $company = Customer::where('id', $validated['company_id'])
            ->where('customer_type', 'company')
            ->firstOrFail();

        $rawNumber = trim($validated['vehicle_number']);
        $normalized = Vehicle::normalizeNumber($rawNumber);

        // Find existing vehicle by normalized number
        $existing = Vehicle::with('customer')
            ->where(function ($q) use ($normalized, $rawNumber) {
                $q->where('vehicle_number_normalized', $normalized)
                    ->orWhereRaw("UPPER(REPLACE(REPLACE(vehicle_number, '-', ''), ' ', '')) = ?", [$normalized])
                    ->orWhere('vehicle_number', strtoupper($rawNumber));
            })
            ->first();

        $brand = ! empty($validated['brand']) ? trim($validated['brand']) : null;
        $model = ! empty($validated['model']) ? trim($validated['model']) : null;
        $makeModel = trim("{$brand} {$model}") ?: null;

        if ($existing) {
            // If already belongs to this company
            if ($existing->customer_id === $company->id) {
                return back()->withInput()->with('warning', "Vehicle {$existing->vehicle_number} is already registered under {$company->company_name}.");
            }

            // Belongs to another owner / company
            $currentOwner = $existing->customer ? ($existing->customer->company_name ?: $existing->customer->customer_name) : 'Unknown Owner';

            if (! $request->boolean('transfer_confirmed')) {
                return back()->withInput()->with('transfer_required', [
                    'vehicle_id' => $existing->id,
                    'vehicle_number' => $existing->vehicle_number,
                    'current_owner' => $currentOwner,
                    'target_company' => $company->company_name,
                    'target_company_id' => $company->id,
                ])->with('warning', "Vehicle {$existing->vehicle_number} is currently registered under '{$currentOwner}'. Please confirm if you wish to transfer ownership to {$company->company_name}.");
            }

            // Transfer ownership to this company
            DB::transaction(function () use ($existing, $company, $validated, $brand, $model, $makeModel, $user, $currentOwner) {
                $existing->update([
                    'customer_id' => $company->id,
                    'vehicle_type_id' => $validated['vehicle_type_id'],
                    'brand' => $brand ?? $existing->brand,
                    'model' => $model ?? $existing->model,
                    'make_model' => $makeModel ?? $existing->make_model,
                    'year_of_manufacture' => $validated['year_of_manufacture'] ?? $existing->year_of_manufacture,
                    'driver_name' => $validated['driver_name'] ?? $existing->driver_name,
                    'driver_mobile' => $validated['driver_mobile'] ?? $existing->driver_mobile,
                    'current_mileage' => $validated['current_mileage'] ?? $existing->current_mileage,
                    'next_service_mileage' => $validated['next_service_mileage'] ?? $existing->next_service_mileage,
                    'notes' => $validated['notes'] ?? $existing->notes,
                ]);

                ActivityLog::log(
                    action: 'transferred_vehicle_to_company',
                    description: "Transferred vehicle {$existing->vehicle_number} from '{$currentOwner}' to company '{$company->company_name}'. Historical service records preserved.",
                    module: 'Company & Vehicles',
                    userId: $user->id
                );
            });

            return redirect()->route('operations.company-vehicles.index', ['selected_id' => $company->id])
                ->with('success', "Vehicle {$existing->vehicle_number} transferred successfully to {$company->company_name}.");
        }

        // New vehicle creation
        $activeBranchId = BranchContext::getActiveBranchId();

        $vehicle = Vehicle::create([
            'customer_id' => $company->id,
            'branch_id' => $activeBranchId,
            'vehicle_type_id' => $validated['vehicle_type_id'],
            'vehicle_number' => strtoupper($rawNumber),
            'vehicle_number_normalized' => $normalized,
            'brand' => $brand,
            'model' => $model,
            'make_model' => $makeModel,
            'year_of_manufacture' => $validated['year_of_manufacture'] ?? null,
            'driver_name' => $validated['driver_name'] ?? null,
            'driver_mobile' => $validated['driver_mobile'] ?? null,
            'current_mileage' => $validated['current_mileage'] ?? 0,
            'next_service_mileage' => $validated['next_service_mileage'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        ActivityLog::log(
            action: 'registered_company_vehicle',
            description: "Registered vehicle {$vehicle->vehicle_number} under company '{$company->company_name}'.",
            module: 'Company & Vehicles',
            userId: $user->id
        );

        return redirect()->route('operations.company-vehicles.index', ['selected_id' => $company->id])
            ->with('success', "Vehicle {$vehicle->vehicle_number} added successfully to {$company->company_name}.");
    }

    /**
     * Update an existing company vehicle and driver details.
     */
    public function updateVehicle(Request $request, Vehicle $vehicle): RedirectResponse|JsonResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'vehicle_number' => ['required', 'string', 'max:50'],
            'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'year_of_manufacture' => ['nullable', 'integer', 'min:1960', 'max:'.(date('Y') + 1)],
            'driver_name' => ['nullable', 'string', 'max:150'],
            'driver_mobile' => ['nullable', 'string', 'max:50'],
            'current_mileage' => ['nullable', 'integer', 'min:0'],
            'next_service_mileage' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $rawNumber = trim($validated['vehicle_number']);
        $normalized = Vehicle::normalizeNumber($rawNumber);

        // Check if another vehicle already has this vehicle number
        $duplicate = Vehicle::where('id', '!=', $vehicle->id)
            ->where(function ($q) use ($normalized, $rawNumber) {
                $q->where('vehicle_number_normalized', $normalized)
                    ->orWhereRaw("UPPER(REPLACE(REPLACE(vehicle_number, '-', ''), ' ', '')) = ?", [$normalized])
                    ->orWhere('vehicle_number', strtoupper($rawNumber));
            })
            ->first();

        if ($duplicate) {
            $owner = $duplicate->customer ? ($duplicate->customer->company_name ?: $duplicate->customer->customer_name) : 'another customer';
            $msg = "Vehicle number '{$rawNumber}' is already registered in the system (Owner: {$owner}).";
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $msg,
                    'errors' => ['vehicle_number' => [$msg]],
                ], 422);
            }
            return back()->withInput()->with('error', $msg);
        }

        $brand = ! empty($validated['brand']) ? trim($validated['brand']) : null;
        $model = ! empty($validated['model']) ? trim($validated['model']) : null;
        $makeModel = trim("{$brand} {$model}") ?: ($vehicle->make_model ?? null);

        $vehicle->update([
            'vehicle_number' => strtoupper($rawNumber),
            'vehicle_number_normalized' => $normalized,
            'vehicle_type_id' => $validated['vehicle_type_id'],
            'brand' => $brand,
            'model' => $model,
            'make_model' => $makeModel,
            'year_of_manufacture' => $validated['year_of_manufacture'] ?? null,
            'driver_name' => $validated['driver_name'] ?? null,
            'driver_mobile' => $validated['driver_mobile'] ?? null,
            'current_mileage' => $validated['current_mileage'] ?? $vehicle->current_mileage,
            'next_service_mileage' => $validated['next_service_mileage'] ?? $vehicle->next_service_mileage,
            'notes' => $validated['notes'] ?? null,
        ]);

        ActivityLog::log(
            action: 'updated_company_vehicle',
            description: "Updated vehicle details for {$vehicle->vehicle_number} (Driver: {$vehicle->driver_name}).",
            module: 'Company & Vehicles',
            userId: $user->id
        );

        $successMessage = "Vehicle {$vehicle->vehicle_number} updated successfully.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'vehicle' => $vehicle->fresh(['vehicleType']),
            ]);
        }

        return redirect()->route('operations.company-vehicles.index', ['selected_id' => $vehicle->customer_id])
            ->with('success', $successMessage);
    }

    /**
     * Live duplicate and transfer check for a vehicle number.
     */
    public function checkVehicle(Request $request): JsonResponse
    {
        $rawNumber = trim((string) $request->input('number', ''));
        $targetCompanyId = (int) $request->input('company_id', 0);

        if ($rawNumber === '') {
            return response()->json(['exists' => false]);
        }

        $normalized = Vehicle::normalizeNumber($rawNumber);

        $vehicle = Vehicle::with('customer')
            ->where(function ($q) use ($normalized, $rawNumber) {
                $q->where('vehicle_number_normalized', $normalized)
                    ->orWhereRaw("UPPER(REPLACE(REPLACE(vehicle_number, '-', ''), ' ', '')) = ?", [$normalized])
                    ->orWhere('vehicle_number', strtoupper($rawNumber));
            })
            ->first();

        if (! $vehicle) {
            return response()->json(['exists' => false]);
        }

        $currentOwner = $vehicle->customer ? ($vehicle->customer->company_name ?: $vehicle->customer->customer_name) : 'Unknown';
        $isSameCompany = ($vehicle->customer_id === $targetCompanyId);

        return response()->json([
            'exists' => true,
            'vehicle_id' => $vehicle->id,
            'vehicle_number' => $vehicle->vehicle_number,
            'vehicle_type_id' => $vehicle->vehicle_type_id,
            'brand' => $vehicle->brand,
            'model' => $vehicle->model,
            'make_model' => $vehicle->make_model,
            'year' => $vehicle->year_of_manufacture,
            'driver_name' => $vehicle->driver_name,
            'driver_mobile' => $vehicle->driver_mobile,
            'current_mileage' => $vehicle->current_mileage,
            'next_service_mileage' => $vehicle->next_service_mileage,
            'current_owner_id' => $vehicle->customer_id,
            'current_owner_name' => $currentOwner,
            'is_same_company' => $isSameCompany,
        ]);
    }

    /**
     * Return vehicle complete service history.
     */
    public function vehicleHistory(Vehicle $vehicle): JsonResponse
    {
        $jobs = JobCard::where('vehicle_id', $vehicle->id)
            ->with(['services', 'products', 'assignedTechnician', 'invoice', 'issues', 'branch'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'job_number' => $job->job_number,
                    'date' => $job->date ? $job->date->format('d M Y') : ($job->created_at ? $job->created_at->format('d M Y') : '-'),
                    'services' => $job->services->pluck('service_name')->toArray(),
                    'products' => $job->products->map(fn ($p) => "{$p->product_name} ({$p->quantity})")->toArray(),
                    'technician' => $job->assignedTechnician?->name ?? 'Unassigned',
                    'mileage' => $job->current_mileage > 0 ? number_format($job->current_mileage).' km' : '-',
                    'additional_issues' => $job->issues->pluck('description')->toArray(),
                    'invoice_number' => $job->invoice?->invoice_number ?? '-',
                    'grand_total' => (float) $job->grand_total,
                    'status' => ucfirst(str_replace('_', ' ', $job->status)),
                    'status_raw' => $job->status,
                    'branch' => $job->branch?->name ?? 'Main Branch',
                    'view_url' => route('operations.job-cards.show', $job->id),
                ];
            });

        return response()->json([
            'vehicle' => [
                'id' => $vehicle->id,
                'vehicle_number' => $vehicle->vehicle_number,
                'type' => $vehicle->vehicleType?->name ?? 'Vehicle',
                'make_model' => $vehicle->make_model ?: '-',
                'driver_name' => $vehicle->driver_name ?: '-',
                'current_mileage' => $vehicle->current_mileage > 0 ? number_format($vehicle->current_mileage).' km' : '-',
            ],
            'history' => $jobs,
        ]);
    }

    /**
     * Return company invoices with filters.
     */
    public function invoices(Customer $customer, Request $request): JsonResponse
    {
        if ($customer->customer_type !== 'company') {
            return response()->json(['invoices' => []], 404);
        }

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $vehicleId = $request->input('vehicle_id');
        $paymentStatus = $request->input('status');

        $query = $customer->invoices()
            ->with(['jobCard.vehicle', 'payments'])
            ->where('status', '!=', 'cancelled');

        if ($dateFrom) {
            $query->whereDate('invoice_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('invoice_date', '<=', $dateTo);
        }
        if ($vehicleId) {
            $query->whereHas('jobCard', fn ($jq) => $jq->where('vehicle_id', $vehicleId));
        }
        if ($paymentStatus && $paymentStatus !== 'all') {
            $query->where('status', $paymentStatus);
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->get()->map(function ($inv) {
            return [
                'id' => $inv->id,
                'invoice_number' => $inv->invoice_number,
                'invoice_date' => $inv->invoice_date ? $inv->invoice_date->format('d M Y') : 'N/A',
                'due_date' => $inv->due_date ? $inv->due_date->format('d M Y') : 'N/A',
                'vehicle_number' => $inv->jobCard?->vehicle?->vehicle_number ?? ($inv->customer_snapshot['vehicle_number'] ?? 'N/A'),
                'job_number' => $inv->jobCard?->job_number ?? 'N/A',
                'grand_total' => (float) $inv->grand_total,
                'amount_paid' => (float) $inv->amount_paid,
                'balance_amount' => (float) $inv->balance_amount,
                'status' => $inv->status,
                'status_label' => ucfirst(str_replace('_', ' ', $inv->status)),
                'view_url' => route('operations.invoices.show', $inv),
                'thermal_url' => route('operations.invoices.thermal', $inv),
            ];
        });

        return response()->json([
            'invoices' => $invoices,
            'summary' => [
                'total_amount' => (float) $invoices->sum('grand_total'),
                'total_paid' => (float) $invoices->sum('amount_paid'),
                'total_balance' => (float) $invoices->sum('balance_amount'),
            ],
        ]);
    }

    /**
     * Generate / view Company Monthly Statement.
     */
    public function statement(Customer $customer, Request $request): RedirectResponse
    {
        if ($customer->customer_type !== 'company') {
            abort(404, 'Customer is not a company.');
        }

        $creditAccount = $customer->creditAccount;
        if (! $creditAccount) {
            $creditAccount = CustomerCreditAccount::create([
                'customer_id' => $customer->id,
                'credit_limit' => 0.00,
                'payment_terms_days' => 30,
                'opening_balance' => 0.00,
                'status' => 'active',
                'created_by' => Auth::id() ?? 1,
            ]);
        }

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        return redirect()->route('reports.credit-management.statement', [
            'creditAccount' => $creditAccount,
            'month' => $month,
            'year' => $year,
        ]);
    }
}

