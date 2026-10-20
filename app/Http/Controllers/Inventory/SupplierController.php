<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreSupplierRequest;
use App\Http\Requests\Inventory\UpdateSupplierRequest;
use App\Models\ActivityLog;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $status = $request->input('status');

        $query = Supplier::query();

        if ($search !== '') {
            $query->search($search);
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $totalSuppliers = Supplier::count();
        $suppliers = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('inventory.suppliers.index', compact('suppliers', 'search', 'status', 'totalSuppliers'));
    }

    /**
     * Show the form for creating a new supplier.
     */
    public function create(Request $request): View
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Administrator access is required to add suppliers.');
        }

        return view('inventory.suppliers.create');
    }

    /**
     * Store a newly created supplier in storage.
     */
    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $supplier = Supplier::create($data);

        ActivityLog::log(
            action: 'created',
            description: "Registered new supplier: {$supplier->name}.",
            module: 'Suppliers'
        );

        return redirect()->route('inventory.suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    /**
     * Display the specified supplier details.
     */
    public function show(Supplier $supplier): View
    {
        $supplier->load(['products.uom', 'goodsReceivedNotes' => function ($q) {
            $q->latest('received_date')->take(5);
        }]);

        return view('inventory.suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified supplier.
     */
    public function edit(Request $request, Supplier $supplier): View
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Administrator access is required to edit suppliers.');
        }

        return view('inventory.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified supplier in storage.
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $data = $request->validated();
        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $supplier->update($data);

        ActivityLog::log(
            action: 'updated',
            description: "Updated supplier details for: {$supplier->name}.",
            module: 'Suppliers'
        );

        return redirect()->route('inventory.suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    /**
     * Toggle the active/inactive status of a supplier.
     */
    public function toggleStatus(Request $request, Supplier $supplier): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Administrator access is required.');
        }

        $newStatus = ! $supplier->is_active;
        $supplier->update(['is_active' => $newStatus]);

        $statusLabel = $newStatus ? 'activated' : 'deactivated';

        ActivityLog::log(
            action: 'status_changed',
            description: "Supplier {$supplier->name} was {$statusLabel}.",
            module: 'Suppliers'
        );

        return back()->with('success', "Supplier '{$supplier->name}' has been {$statusLabel}.");
    }

    /**
     * Export supplier list to Excel (CSV compatible format).
     */
    public function export(Request $request): StreamedResponse
    {
        $search = trim((string) $request->input('search'));

        $query = Supplier::query();
        if ($search !== '') {
            $query->search($search);
        }
        $suppliers = $query->orderBy('name', 'asc')->get();

        $fileName = 'suppliers_export_'.now()->format('Y_m_d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->streamDownload(function () use ($suppliers) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Supplier Name',
                'Registration No',
                'Mobile',
                'Office Phone',
                'Fax',
                'Address',
                'Status',
                'Created At',
            ]);

            foreach ($suppliers as $s) {
                fputcsv($file, [
                    $s->name,
                    $s->registration_number ?? '',
                    $s->mobile ?? '',
                    $s->office_phone ?? '',
                    $s->fax ?? '',
                    $s->address ?? '',
                    $s->is_active ? 'Active' : 'Inactive',
                    $s->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        }, $fileName, $headers);
    }

    /**
     * Delete supplier (protected if purchases exist).
     */
    public function destroy(Request $request, Supplier $supplier): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Administrator access is required.');
        }

        if ($supplier->hasPurchases()) {
            return back()->with('error', "Cannot delete supplier '{$supplier->name}' because purchase/GRN transactions exist. You may deactivate the supplier instead.");
        }

        $supplier->delete();

        ActivityLog::log(
            action: 'deleted',
            description: "Deleted supplier: {$supplier->name}.",
            module: 'Suppliers'
        );

        return redirect()->route('inventory.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
