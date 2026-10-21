<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Display customer listing.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $query = Customer::withCount(['vehicles', 'jobCards', 'invoices'])
            ->orderBy('id', 'desc');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(15)->withQueryString();

        return view('operations.customers.index', compact('customers', 'search'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create(): View
    {
        return view('operations.customers.create');
    }

    /**
     * Store a newly created customer in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'customer_type' => ['nullable', 'in:individual,company'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $phone = trim($validated['phone']);

        // Prevent duplicate customer by phone
        $existing = Customer::where('phone', $phone)->first();
        if ($existing) {
            return redirect()->route('operations.vehicles.index', ['search' => $existing->phone])
                ->with('warning', "Customer '{$existing->customer_name}' already exists with phone {$existing->phone}. You can link a vehicle to this customer.");
        }

        $customer = Customer::create([
            'customer_name' => trim($validated['customer_name']),
            'phone' => $phone,
            'email' => ! empty($validated['email']) ? trim($validated['email']) : null,
            'address' => $validated['address'] ?? null,
            'customer_type' => $validated['customer_type'] ?? 'individual',
            'company_name' => $validated['company_name'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'active',
        ]);

        // Audit log
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'module' => 'Customers',
            'description' => "Registered new customer '{$customer->customer_name}' ({$customer->phone}).",
        ]);

        return redirect()->route('operations.vehicles.index', ['customer_id' => $customer->id])
            ->with('success', "Customer '{$customer->customer_name}' registered successfully. Proceed to register vehicle & create job card.");
    }
}
