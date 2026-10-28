<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\CarpetCategory;
use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JobCard;
use App\Models\JobCardProduct;
use App\Models\JobCardService;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceVehiclePrice;
use App\Models\StockBatch;
use App\Models\User;
use App\Services\BranchContext;
use App\Services\Inventory\StockService;
use App\Services\InvoiceNumberService;
use App\Services\QuantityFormatter;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JobCardController extends Controller
{
    public function __construct(
        protected StockService $stockService
    ) {}

    /**
     * Display listing of job cards with status filters and quick search.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $search = trim((string) $request->query('search', ''));
        $dateFilter = $request->query('filter', '');

        $query = JobCard::with(['vehicle', 'customer', 'vehicleType', 'assignedTechnician', 'invoice'])
            ->orderBy('id', 'desc');

        $today = now('Asia/Colombo')->toDateString();

        // Apply Tab / Status Filters
        if ($dateFilter === 'today') {
            $query->whereDate('date', $today);
        } elseif ($dateFilter === 'previous_pending') {
            $query->where('status', 'pending')->whereDate('date', '<', $today);
        } elseif ($status !== 'all' && in_array($status, ['pending', 'in_progress', 'completed', 'delivered', 'cancelled'])) {
            $query->where('status', $status);
        }

        // Search Filter
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('job_number', 'like', "%{$search}%")
                    ->orWhereHas('vehicle', function ($vq) use ($search) {
                        $vq->where('vehicle_number', 'like', "%{$search}%")
                            ->orWhere('vehicle_number_normalized', 'like', '%'.strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $search)).'%');
                    })
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('customer_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $jobs = $query->paginate(15)->withQueryString();

        // Counters for Quick Status Tabs
        $counts = [
            'all' => JobCard::count(),
            'today' => JobCard::whereDate('date', $today)->count(),
            'pending' => JobCard::where('status', 'pending')->count(),
            'in_progress' => JobCard::where('status', 'in_progress')->count(),
            'completed' => JobCard::where('status', 'completed')->count(),
            'previous_pending' => JobCard::where('status', 'pending')->whereDate('date', '<', $today)->count(),
        ];

        return view('operations.jobs.index', compact('jobs', 'status', 'search', 'dateFilter', 'counts'));
    }

    /**
     * Display a specific job card with all services, products, and available actions.
     */
    public function show(JobCard $jobCard): View
    {
        $jobCard->load([
            'vehicle',
            'customer.creditAccount',
            'vehicleType.carpetCategories',
            'carpets.carpetCategory',
            'assignedTechnician',
            'creator',
            'invoice.payments.creator',
            'supplementaryInvoices',
            'services.service',
            'products.product',
            'issues.approvedByUser',
            'issues.recordedByUser',
            'payments.creator',
        ]);

        $resolvedInvoice = $jobCard->getResolvedInvoice();

        $availableServices = Service::active()->with('vehiclePrices')->orderBy('name')->get();

        // Products with stock availability
        $availableProducts = Product::active()->orderBy('name')->get()->map(function ($product) {
            $product->available_stock = $this->stockService->getAvailableStock($product);

            return $product;
        });

        // Technicians for assignment
        $technicians = User::where('status', 'active')->orderBy('name')->get();

        return view('operations.jobs.show', compact('jobCard', 'resolvedInvoice', 'availableServices', 'availableProducts', 'technicians'));
    }

    /**
     * Fetch service price configured for the job card's vehicle type.
     */
    public function getServicePrice(JobCard $jobCard, Service $service): JsonResponse
    {
        $pricing = ServiceVehiclePrice::where('service_id', $service->id)
            ->where('vehicle_type_id', $jobCard->vehicle_type_id)
            ->first();

        if (! $pricing || $pricing->service_price === null) {
            $vtName = $jobCard->vehicleType?->name ?? 'this vehicle type';

            return response()->json([
                'configured' => false,
                'message' => "Price not configured for {$vtName}.",
                'price' => null,
            ]);
        }

        return response()->json([
            'configured' => true,
            'price' => (float) $pricing->service_price,
            'cost' => (float) ($pricing->service_cost ?? 0),
        ]);
    }

    /**
     * Add a service to the job card (supports direct-add and AJAX).
     */
    public function addService(Request $request, JobCard $jobCard): JsonResponse|RedirectResponse
    {
        if (in_array($jobCard->status, ['completed', 'delivered', 'cancelled'])) {
            $msg = 'Cannot modify services on a finished or cancelled job.';

            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }

        $validated = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'custom_price' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'numeric', 'min:0.1'],
            'technician_name' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
            'job_issue_id' => ['nullable', 'exists:job_issues,id'],
        ]);

        try {
            $service = Service::findOrFail($validated['service_id']);

            // Check if price is configured for this vehicle type
            $pricing = ServiceVehiclePrice::where('service_id', $service->id)
                ->where('vehicle_type_id', $jobCard->vehicle_type_id)
                ->first();

            $user = Auth::user();
            $isPriceCustom = false;
            $isPricePending = false;
            $unitPrice = 0.0;

            if (isset($validated['custom_price']) && $validated['custom_price'] !== null && $validated['custom_price'] !== '' && ($user->isAdmin() || $user->hasPermission('override_prices'))) {
                $unitPrice = (float) $validated['custom_price'];
                $isPriceCustom = true;
                $isPricePending = false;
            } elseif ($pricing && $pricing->service_price !== null && (float) $pricing->service_price > 0) {
                $unitPrice = (float) $pricing->service_price;
                $isPricePending = false;
            } else {
                // If not configured, allow adding with pending price
                $unitPrice = 0.0;
                $isPricePending = true;
            }

            $qty = ! empty($validated['quantity']) ? (float) $validated['quantity'] : 1.0;
            $technicianName = ! empty($validated['technician_name'])
                ? $validated['technician_name']
                : ($jobCard->assignedTechnician?->name ?? null);

            // If the service already exists in the Job Cart and does not have custom pricing, increment its quantity
            $existingItem = $jobCard->services()
                ->where('service_id', $service->id)
                ->whereNull('invoice_id')
                ->first();

            if ($existingItem && ! $isPriceCustom) {
                $newQty = (float) $existingItem->quantity + $qty;
                $existingItem->update([
                    'quantity' => $newQty,
                    'line_total' => $existingItem->is_price_pending ? 0.0 : round($existingItem->unit_price * $newQty, 2),
                ]);
                $jobCard->recalculateTotals();

                $msg = "Quantity for service '{$service->name}' increased to {$newQty}.";

                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $msg,
                        'cart' => $this->getJobCardCartPayload($jobCard),
                        'item_id' => $existingItem->id,
                    ]);
                }

                return back()->with('success', $msg)->with('newly_added_service_id', $existingItem->id);
            }

            $lineTotal = $isPricePending ? 0.0 : round($unitPrice * $qty, 2);
            $isAdditional = ($jobCard->status === 'additional_work') || ($jobCard->invoice_id !== null);

            $serviceItem = $jobCard->services()->create([
                'service_id' => $service->id,
                'service_code' => $service->code,
                'service_name' => $service->name,
                'unit_price' => $unitPrice,
                'quantity' => $qty,
                'line_total' => $lineTotal,
                'is_price_custom' => $isPriceCustom,
                'is_price_pending' => $isPricePending,
                'is_additional' => $isAdditional,
                'job_issue_id' => $validated['job_issue_id'] ?? null,
                'technician_name' => $technicianName,
                'notes' => $validated['notes'] ?? null,
            ]);

            $jobCard->recalculateTotals();

            $msg = $isPricePending
                ? "Service '{$service->name}' added (Price Pending)."
                : "Service '{$service->name}' added to Job Card.";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'cart' => $this->getJobCardCartPayload($jobCard),
                    'item_id' => $serviceItem->id,
                ]);
            }

            return back()->with('success', $msg)->with('newly_added_service_id', $serviceItem->id);
        } catch (\Throwable $e) {
            $msg = $this->handleOperationException($e, 'add service', $jobCard);

            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }
    }

    /**
     * Update service quantity (increment / decrement).
     */
    public function updateServiceQuantity(Request $request, JobCard $jobCard, JobCardService $serviceItem): JsonResponse|RedirectResponse
    {
        if (in_array($jobCard->status, ['completed', 'delivered', 'cancelled'])) {
            $msg = 'Cannot modify services on a finished or cancelled job.';

            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }

        if ($serviceItem->invoice_id) {
            $msg = 'Cannot modify a service that has already been invoiced.';

            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }

        if ($serviceItem->job_card_id !== $jobCard->id) {
            abort(403);
        }

        try {
            $action = $request->input('action');
            $currentQty = (float) $serviceItem->quantity;

            if ($action === 'increase') {
                $newQty = $currentQty + 1;
            } elseif ($action === 'decrease') {
                $newQty = $currentQty - 1;
            } elseif ($request->has('quantity')) {
                $newQty = (float) $request->input('quantity');
            } else {
                $newQty = $currentQty;
            }

            if ($newQty <= 0) {
                $serviceName = $serviceItem->service_name;
                $serviceItem->delete();
                $jobCard->recalculateTotals();
                $msg = "Service '{$serviceName}' removed from Job Card.";
            } else {
                $serviceItem->update([
                    'quantity' => $newQty,
                    'line_total' => $serviceItem->is_price_pending ? 0.0 : round($serviceItem->unit_price * $newQty, 2),
                ]);
                $jobCard->recalculateTotals();
                $msg = "Service '{$serviceItem->service_name}' quantity updated to {$newQty}.";
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'cart' => $this->getJobCardCartPayload($jobCard),
                ]);
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            $msg = $this->handleOperationException($e, 'update service quantity', $jobCard);

            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }
    }

    /**
     * Update service item details (technician, price override, notes).
     */
    public function updateServiceItem(Request $request, JobCard $jobCard, JobCardService $serviceItem): JsonResponse|RedirectResponse
    {
        if (in_array($jobCard->status, ['completed', 'delivered', 'cancelled'])) {
            $msg = 'Cannot modify services on a finished or cancelled job.';

            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }

        if ($serviceItem->invoice_id) {
            $msg = 'Cannot modify a service that has already been invoiced.';

            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }

        if ($serviceItem->job_card_id !== $jobCard->id) {
            abort(403);
        }

        $validated = $request->validate([
            'technician_name' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
            'custom_price' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'numeric', 'min:0.01'],
        ]);

        try {
            $user = Auth::user();
            $updateData = [
                'technician_name' => $validated['technician_name'] ?? $serviceItem->technician_name,
                'notes' => $validated['notes'] ?? $serviceItem->notes,
            ];

            if (isset($validated['quantity']) && (float) $validated['quantity'] > 0) {
                $updateData['quantity'] = (float) $validated['quantity'];
            }

            $canOverride = $user->isAdmin() || $user->hasPermission('override_prices');
            if (isset($validated['custom_price']) && ($canOverride || $serviceItem->is_price_pending)) {
                $oldPrice = (float) $serviceItem->unit_price;
                $newPrice = (float) $validated['custom_price'];
                $qty = $updateData['quantity'] ?? (float) $serviceItem->quantity;
                $updateData['unit_price'] = $newPrice;
                $updateData['line_total'] = round($newPrice * $qty, 2);
                $updateData['is_price_custom'] = true;
                $updateData['is_price_pending'] = false;

                ActivityLog::log(
                    action: 'price_updated',
                    description: "Updated price for service '{$serviceItem->service_name}' on Job #{$jobCard->job_number} from LKR {$oldPrice} to LKR {$newPrice} by {$user->name}.",
                    module: 'Job Cards'
                );
            } elseif (isset($updateData['quantity'])) {
                $price = (float) $serviceItem->unit_price;
                $updateData['line_total'] = $serviceItem->is_price_pending ? 0.0 : round($price * $updateData['quantity'], 2);
            }

            $serviceItem->update($updateData);
            $jobCard->recalculateTotals();

            $msg = "Service '{$serviceItem->service_name}' updated successfully.";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'cart' => $this->getJobCardCartPayload($jobCard),
                ]);
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            $msg = $this->handleOperationException($e, 'update service', $jobCard);

            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }
    }

    /**
     * Remove a service from the job card.
     */
    public function removeService(Request $request, JobCard $jobCard, JobCardService $serviceItem): JsonResponse|RedirectResponse
    {
        if (in_array($jobCard->status, ['completed', 'delivered', 'cancelled'])) {
            $msg = 'Cannot modify services on a finished or cancelled job.';

            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }

        if ($serviceItem->invoice_id) {
            $msg = 'Cannot remove a service that has already been invoiced.';

            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }

        if ($serviceItem->job_card_id !== $jobCard->id) {
            abort(403);
        }

        try {
            $serviceName = $serviceItem->service_name;
            $serviceItem->delete();
            $jobCard->recalculateTotals();

            $msg = "Service '{$serviceName}' removed from Job Card.";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'cart' => $this->getJobCardCartPayload($jobCard),
                ]);
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            $msg = $this->handleOperationException($e, 'remove service', $jobCard);

            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }
    }

    /**
     * Add product/part to job card and confirm stock deduction using FIFO (supports direct-add and AJAX).
     */
    public function addProduct(Request $request, JobCard $jobCard): JsonResponse|RedirectResponse
    {
        if (in_array($jobCard->status, ['completed', 'delivered', 'cancelled'])) {
            $msg = 'Cannot add products to a finished or cancelled job.';

            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }

        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'numeric', 'min:0.01'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
            'job_issue_id' => ['nullable', 'exists:job_issues,id'],
        ]);

        $product = Product::with('uom')->findOrFail($validated['product_id']);
        $quantity = ! empty($validated['quantity']) ? (float) $validated['quantity'] : 1.0;

        if (! $product->allowsFractionalQuantity() && floor($quantity) != $quantity) {
            $msg = "Product '{$product->product_name}' ({$product->uom?->name}) requires a whole-number quantity.";

            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg)->withInput();
        }

        $availableStock = $this->stockService->getAvailableStock($product);
        if ($availableStock < $quantity) {
            $msg = "Insufficient stock for '{$product->product_name}'. Requested: {$quantity}, Available: {$availableStock}.";

            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }

        // Selling price defaults to product selling price, or pending if not set
        $user = Auth::user();
        $isPricePending = false;
        $unitPrice = 0.0;

        if (isset($validated['unit_price']) && $validated['unit_price'] !== null && $validated['unit_price'] !== '' && ($user->isAdmin() || $user->hasPermission('override_prices'))) {
            $unitPrice = (float) $validated['unit_price'];
            $isPricePending = false;
        } elseif ($product->selling_price !== null && (float) $product->selling_price > 0) {
            $unitPrice = (float) $product->selling_price;
            $isPricePending = false;
        } else {
            $unitPrice = 0.0;
            $isPricePending = true;
        }

        $isAdditional = ($jobCard->status === 'additional_work') || ($jobCard->invoice_id !== null);

        $productItem = null;
        try {
            DB::transaction(function () use ($jobCard, $product, $quantity, $unitPrice, $isPricePending, $validated, $isAdditional, &$productItem) {
                // Deduct stock using FIFO atomically
                $issueResult = $this->stockService->issueStockFifo(
                    $product,
                    $quantity,
                    'job_card_issue',
                    $jobCard,
                    Auth::user()
                );

                // If already exists in cart without invoice, merge quantity
                $existingItem = $jobCard->products()
                    ->where('product_id', $product->id)
                    ->whereNull('invoice_id')
                    ->first();

                if ($existingItem) {
                    $existingAllocations = $existingItem->batch_allocations ?? [];
                    $mergedAllocations = array_merge($existingAllocations, $issueResult['batches']);

                    $newQty = (float) $existingItem->quantity + $quantity;
                    $newTotalCost = ((float) $existingItem->unit_cost * (float) $existingItem->quantity) + $issueResult['total_cost'];
                    $newUnitCost = $newQty > 0 ? round($newTotalCost / $newQty, 4) : 0.0;
                    $newLineTotal = $existingItem->is_price_pending ? 0.0 : round($existingItem->unit_price * $newQty, 2);

                    $existingItem->update([
                        'quantity' => $newQty,
                        'unit_cost' => $newUnitCost,
                        'line_total' => $newLineTotal,
                        'batch_allocations' => $mergedAllocations,
                    ]);

                    $productItem = $existingItem;
                } else {
                    $unitCost = $quantity > 0 ? round($issueResult['total_cost'] / $quantity, 4) : 0.0;
                    $lineTotal = $isPricePending ? 0.0 : round($unitPrice * $quantity, 2);

                    $productItem = $jobCard->products()->create([
                        'product_id' => $product->id,
                        'product_sku' => $product->sku,
                        'product_name' => $product->product_name,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'unit_cost' => $unitCost,
                        'line_total' => $lineTotal,
                        'is_stock_deducted' => true,
                        'is_price_pending' => $isPricePending,
                        'is_additional' => $isAdditional,
                        'job_issue_id' => $validated['job_issue_id'] ?? null,
                        'batch_allocations' => $issueResult['batches'],
                        'notes' => $validated['notes'] ?? null,
                    ]);
                }

                $jobCard->recalculateTotals();
            });

            $fmtQty = QuantityFormatter::format($quantity);
            $msg = $isPricePending
                ? "Product '{$product->product_name}' (Qty: {$fmtQty}) added (Price Pending)."
                : "Product '{$product->product_name}' (Qty: {$fmtQty}) added to Job Card.";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'cart' => $this->getJobCardCartPayload($jobCard),
                    'item_id' => $productItem?->id,
                ]);
            }

            return back()->with('success', $msg)->with('newly_added_product_id', $productItem?->id);
        } catch (\Throwable $e) {
            $msg = $this->handleOperationException($e, 'add product', $jobCard);

            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }
    }

    /**
     * Update product quantity (increment with FIFO issue / decrement with LIFO reversal).
     */
    public function updateProductQuantity(Request $request, JobCard $jobCard, JobCardProduct $productItem): JsonResponse|RedirectResponse
    {
        if (in_array($jobCard->status, ['completed', 'delivered', 'cancelled'])) {
            $msg = 'Cannot modify products on a finished or cancelled job.';

            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }

        if ($productItem->invoice_id) {
            $msg = 'Cannot modify a product that has already been invoiced.';

            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }

        if ($productItem->job_card_id !== $jobCard->id) {
            abort(403);
        }

        $action = $request->input('action');
        $product = Product::findOrFail($productItem->product_id);
        $step = 1.0;

        try {
            DB::transaction(function () use ($jobCard, $productItem, $product, $action, $step) {
                if ($action === 'increase') {
                    $available = $this->stockService->getAvailableStock($product);
                    if ($available < $step) {
                        throw new Exception("Product '{$product->product_name}' is out of stock (Available: {$available}).");
                    }

                    $issueResult = $this->stockService->issueStockFifo(
                        $product,
                        $step,
                        'job_card_issue',
                        $jobCard,
                        Auth::user()
                    );

                    $existingAllocations = $productItem->batch_allocations ?? [];
                    $mergedAllocations = array_merge($existingAllocations, $issueResult['batches']);

                    $newQty = (float) $productItem->quantity + $step;
                    $newTotalCost = ((float) $productItem->unit_cost * (float) $productItem->quantity) + $issueResult['total_cost'];
                    $newUnitCost = $newQty > 0 ? round($newTotalCost / $newQty, 4) : 0.0;
                    $newLineTotal = $productItem->is_price_pending ? 0.0 : round($productItem->unit_price * $newQty, 2);

                    $productItem->update([
                        'quantity' => $newQty,
                        'unit_cost' => $newUnitCost,
                        'line_total' => $newLineTotal,
                        'batch_allocations' => $mergedAllocations,
                    ]);
                } elseif ($action === 'decrease') {
                    $currentQty = (float) $productItem->quantity;

                    if ($currentQty <= $step) {
                        // Full removal
                        if ($productItem->is_stock_deducted && ! empty($productItem->batch_allocations)) {
                            $this->stockService->reverseStockFifo(
                                $productItem->batch_allocations,
                                $jobCard,
                                Auth::user(),
                                "Quantity decreased to 0 on Job Card #{$jobCard->job_number}"
                            );
                        }
                        $productItem->delete();
                    } else {
                        // Partial reversal: reverse 1 unit from allocations in LIFO order
                        $remainingToReverse = $step;
                        $reversedAllocations = [];
                        $keptAllocations = [];
                        $allocations = $productItem->batch_allocations ?? [];

                        for ($i = count($allocations) - 1; $i >= 0; $i--) {
                            $alloc = $allocations[$i];
                            $allocQty = (float) ($alloc['quantity'] ?? 0);
                            $batchId = $alloc['batch_id'] ?? null;
                            $unitCost = $alloc['unit_cost'] ?? 0;

                            if ($remainingToReverse <= 0) {
                                array_unshift($keptAllocations, $alloc);

                                continue;
                            }

                            if ($allocQty <= $remainingToReverse) {
                                $reversedAllocations[] = $alloc;
                                $remainingToReverse -= $allocQty;
                            } else {
                                $reversedAllocations[] = [
                                    'batch_id' => $batchId,
                                    'quantity' => $remainingToReverse,
                                    'unit_cost' => $unitCost,
                                ];
                                $keptAllocations = array_merge([[
                                    'batch_id' => $batchId,
                                    'quantity' => $allocQty - $remainingToReverse,
                                    'unit_cost' => $unitCost,
                                ]], $keptAllocations);
                                $remainingToReverse = 0;
                            }
                        }

                        if (! empty($reversedAllocations)) {
                            $this->stockService->reverseStockFifo(
                                $reversedAllocations,
                                $jobCard,
                                Auth::user(),
                                "Quantity decreased on Job Card #{$jobCard->job_number}"
                            );
                        }

                        $newQty = max(0, $currentQty - $step);
                        $newLineTotal = $productItem->is_price_pending ? 0.0 : round($productItem->unit_price * $newQty, 2);

                        $productItem->update([
                            'quantity' => $newQty,
                            'line_total' => $newLineTotal,
                            'batch_allocations' => $keptAllocations,
                        ]);
                    }
                }

                $jobCard->recalculateTotals();
            });

            $msg = "Quantity updated for '{$productItem->product_name}'.";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'cart' => $this->getJobCardCartPayload($jobCard),
                ]);
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            $msg = $this->handleOperationException($e, 'update product quantity', $jobCard);

            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }
    }

    /**
     * Update product item details (notes, unit price override).
     */
    public function updateProductItem(Request $request, JobCard $jobCard, JobCardProduct $productItem): JsonResponse|RedirectResponse
    {
        if (in_array($jobCard->status, ['completed', 'delivered', 'cancelled'])) {
            $msg = 'Cannot modify products on a finished or cancelled job.';

            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }

        if ($productItem->invoice_id) {
            $msg = 'Cannot modify a product that has already been invoiced.';

            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }

        if ($productItem->job_card_id !== $jobCard->id) {
            abort(403);
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:255'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $user = Auth::user();
            $updateData = [
                'notes' => $validated['notes'] ?? $productItem->notes,
            ];

            $canOverride = $user->isAdmin() || $user->hasPermission('override_prices');
            if (isset($validated['unit_price']) && ($canOverride || $productItem->is_price_pending)) {
                $oldPrice = (float) $productItem->unit_price;
                $newPrice = (float) $validated['unit_price'];
                $updateData['unit_price'] = $newPrice;
                $updateData['line_total'] = round($newPrice * (float) $productItem->quantity, 2);
                $updateData['is_price_pending'] = false;

                ActivityLog::log(
                    action: 'price_updated',
                    description: "Updated selling price for product '{$productItem->product_name}' on Job #{$jobCard->job_number} from LKR {$oldPrice} to LKR {$newPrice} by {$user->name}.",
                    module: 'Job Cards'
                );
            }

            $productItem->update($updateData);
            $jobCard->recalculateTotals();

            $msg = "Part '{$productItem->product_name}' updated successfully.";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'cart' => $this->getJobCardCartPayload($jobCard),
                ]);
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            $msg = $this->handleOperationException($e, 'update product', $jobCard);

            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }
    }

    /**
     * Remove a product from job card and reverse allocated FIFO stock back into batches.
     */
    public function removeProduct(Request $request, JobCard $jobCard, JobCardProduct $productItem): JsonResponse|RedirectResponse
    {
        if (in_array($jobCard->status, ['completed', 'delivered', 'cancelled'])) {
            $msg = 'Cannot remove products from a finished or cancelled job.';

            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }

        if ($productItem->invoice_id) {
            $msg = 'Cannot remove a product that has already been invoiced.';

            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }

        if ($productItem->job_card_id !== $jobCard->id) {
            abort(403);
        }

        try {
            DB::transaction(function () use ($jobCard, $productItem) {
                // Reverse stock if deducted
                if ($productItem->is_stock_deducted && ! empty($productItem->batch_allocations)) {
                    $this->stockService->reverseStockFifo(
                        $productItem->batch_allocations,
                        $jobCard,
                        Auth::user(),
                        "Removed from Job Card #{$jobCard->job_number}"
                    );
                }

                $productItem->delete();
                $jobCard->recalculateTotals();
            });

            $msg = 'Product removed and stock returned to inventory.';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'cart' => $this->getJobCardCartPayload($jobCard),
                ]);
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            $msg = $this->handleOperationException($e, 'remove product', $jobCard);

            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }
    }

    /**
     * Get updated cart payload for frontend reactive rendering.
     */
    protected function getJobCardCartPayload(JobCard $jobCard): array
    {
        $jobCard->load([
            'services',
            'products',
        ]);

        $user = Auth::user();
        $canViewCost = $user?->canViewCostAndProfit() ?? false;
        $vehicleTypeId = $jobCard->vehicle_type_id ?? $jobCard->vehicle?->vehicle_type_id;

        $services = $jobCard->services->map(function ($s) use ($canViewCost, $vehicleTypeId) {
            $serviceCost = null;
            if ($canViewCost && $s->service_id && $vehicleTypeId) {
                $costVal = ServiceVehiclePrice::where('service_id', $s->service_id)
                    ->where('vehicle_type_id', $vehicleTypeId)
                    ->value('service_cost');
                if ($costVal !== null && (float) $costVal > 0) {
                    $serviceCost = (float) $costVal;
                }
            }

            return [
                'id' => $s->id,
                'service_id' => $s->service_id,
                'service_name' => $s->service_name,
                'service_code' => $s->service_code,
                'technician_name' => $s->technician_name,
                'quantity' => (float) $s->quantity,
                'unit_price' => (float) $s->unit_price,
                'unit_cost' => $serviceCost,
                'line_total' => (float) $s->line_total,
                'is_price_custom' => (bool) $s->is_price_custom,
                'is_price_pending' => (bool) $s->is_price_pending,
                'notes' => $s->notes,
            ];
        })->values()->all();

        $products = $jobCard->products->map(function ($p) use ($canViewCost) {
            $unitCost = null;
            if ($canViewCost) {
                if ($p->unit_cost !== null && (float) $p->unit_cost > 0) {
                    $unitCost = (float) $p->unit_cost;
                } else {
                    $batchCost = StockBatch::where('product_id', $p->product_id)
                        ->where('status', 'active')
                        ->where('remaining_quantity', '>', 0)
                        ->orderBy('purchase_date', 'asc')
                        ->orderBy('id', 'asc')
                        ->value('inventory_unit_cost');
                    if ($batchCost !== null && (float) $batchCost > 0) {
                        $unitCost = (float) $batchCost;
                    }
                }
            }

            return [
                'id' => $p->id,
                'product_id' => $p->product_id,
                'product_name' => $p->product_name,
                'product_sku' => $p->product_sku,
                'quantity' => (float) $p->quantity,
                'formatted_quantity' => QuantityFormatter::format((float) $p->quantity),
                'unit_price' => (float) $p->unit_price,
                'unit_cost' => $unitCost,
                'line_total' => (float) $p->line_total,
                'is_price_pending' => (bool) $p->is_price_pending,
                'notes' => $p->notes,
            ];
        })->values()->all();

        $productStocks = [];
        try {
            $activeProducts = Product::where('is_active', true)->select('id')->get();
            foreach ($activeProducts as $p) {
                $productStocks[$p->id] = (float) $this->stockService->getAvailableStock($p->id);
            }
        } catch (\Throwable $e) {
            Log::error("Failed to load product stocks for cart payload on Job Card #{$jobCard->id}: ".$e->getMessage(), [
                'exception' => $e,
                'job_card_id' => $jobCard->id,
            ]);
        }

        $totalCount = count($services) + count($products);

        return [
            'services' => $services,
            'products' => $products,
            'totals' => [
                'service_total' => (float) $jobCard->service_total,
                'product_total' => (float) $jobCard->product_total,
                'discount_amount' => (float) $jobCard->discount_amount,
                'tax_amount' => (float) $jobCard->tax_amount,
                'grand_total' => (float) $jobCard->grand_total,
                'total_count' => $totalCount,
            ],
            'product_stocks' => $productStocks,
        ];
    }

    /**
     * Format a safe, user-friendly error message while logging the full exception.
     */
    protected function handleOperationException(\Throwable $e, string $actionDescription, ?JobCard $jobCard = null): string
    {
        Log::error("Failed to {$actionDescription}".($jobCard ? " for Job Card #{$jobCard->job_number} (ID: {$jobCard->id})" : '').': '.$e->getMessage(), [
            'exception' => $e,
            'job_card_id' => $jobCard?->id,
        ]);

        if ($e instanceof QueryException || str_contains($e->getMessage(), 'SQLSTATE')) {
            return "A system error occurred while processing {$actionDescription}. Please try again.";
        }

        return $e->getMessage();
    }

    /**
     * Update job details (assigned technician, mileage, discount, notes).
     */
    public function update(Request $request, JobCard $jobCard): RedirectResponse
    {
        if ($jobCard->status === 'cancelled') {
            return back()->with('error', 'Cannot edit a cancelled job.');
        }

        $validated = $request->validate([
            'assigned_to' => ['nullable', 'exists:users,id'],
            'current_mileage' => ['nullable', 'integer', 'min:0'],
            'next_service_mileage' => ['nullable', 'integer', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'service_notes' => ['nullable', 'string'],
            'customer_notes' => ['nullable', 'string'],
        ]);

        $oldTechId = $jobCard->assigned_to;
        $newTechId = array_key_exists('assigned_to', $validated) ? $validated['assigned_to'] : $jobCard->assigned_to;

        $updateData = [
            'assigned_to' => $newTechId,
            'current_mileage' => $validated['current_mileage'] ?? $jobCard->current_mileage,
            'next_service_mileage' => $validated['next_service_mileage'] ?? $jobCard->next_service_mileage,
            'service_notes' => $validated['service_notes'] ?? $jobCard->service_notes,
            'customer_notes' => $validated['customer_notes'] ?? $jobCard->customer_notes,
        ];

        // Do not allow staff to rewrite financial discount if already invoiced
        if (! $jobCard->invoice_id && array_key_exists('discount_amount', $validated)) {
            $updateData['discount_amount'] = $validated['discount_amount'] !== null ? (float) $validated['discount_amount'] : 0.0;
        }

        // Log technician assignment / reassignment
        if ($newTechId != $oldTechId) {
            $oldTechName = $jobCard->assignedTechnician?->name ?? 'Unassigned';
            $newTech = $newTechId ? User::find($newTechId) : null;
            $newTechName = $newTech?->name ?? 'Unassigned';
            ActivityLog::log(
                action: 'reassigned_technician',
                description: "Reassigned technician for Job #{$jobCard->job_number} from {$oldTechName} to {$newTechName}.",
                module: 'Job Cards'
            );
        }

        $jobCard->update($updateData);

        // Keep vehicle mileage up-to-date
        if (! empty($validated['current_mileage']) && $validated['current_mileage'] > $jobCard->vehicle->current_mileage) {
            $jobCard->vehicle->update([
                'current_mileage' => $validated['current_mileage'],
                'next_service_mileage' => $validated['next_service_mileage'] ?? $jobCard->vehicle->next_service_mileage,
            ]);
        }

        $jobCard->recalculateTotals();

        return back()->with('success', 'Job Card updated successfully.');
    }

    /**
     * Update carpet categories and quantities for a job card.
     */
    public function updateCarpets(Request $request, JobCard $jobCard): RedirectResponse
    {
        if (in_array($jobCard->status, ['completed', 'delivered', 'cancelled'])) {
            return back()->with('error', 'Cannot modify carpets on a finished or cancelled job.');
        }

        $validated = $request->validate([
            'carpets' => ['nullable', 'array'],
            'carpets.*.category_id' => ['required_with:carpets', 'exists:carpet_categories,id'],
            'carpets.*.quantity' => ['required_with:carpets', 'integer', 'min:0'],
        ]);

        $vehicleType = $jobCard->vehicleType;
        $allowedCatIds = $vehicleType ? $vehicleType->carpetCategories->pluck('id')->all() : [];

        DB::transaction(function () use ($jobCard, $validated, $allowedCatIds) {
            $submittedCategoryIds = [];
            if (! empty($validated['carpets'])) {
                foreach ($validated['carpets'] as $carpetItem) {
                    $catId = (int) ($carpetItem['category_id'] ?? 0);
                    $qty = (int) ($carpetItem['quantity'] ?? 0);
                    if (in_array($catId, $allowedCatIds)) {
                        $submittedCategoryIds[] = $catId;
                        if ($qty > 0) {
                            $cat = CarpetCategory::find($catId);
                            $jobCard->carpets()->updateOrCreate(
                                ['carpet_category_id' => $catId],
                                [
                                    'category_name' => $cat ? $cat->name : 'Carpet',
                                    'quantity' => $qty,
                                ]
                            );
                        } else {
                            $jobCard->carpets()->where('carpet_category_id', $catId)->delete();
                        }
                    }
                }
            }

            $jobCard->carpets()->whereNotIn('carpet_category_id', $submittedCategoryIds)->delete();

            ActivityLog::log(
                action: 'updated_carpets',
                description: "Updated carpet details for Job #{$jobCard->job_number} by ".Auth::user()->name.'.',
                module: 'Job Cards'
            );
        });

        return back()->with('success', 'Carpet details updated successfully.');
    }

    /**
     * Transition job status: Start Work, Complete Job, Mark Delivered.
     */
    public function updateStatus(Request $request, JobCard $jobCard): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:start_work,complete_job,mark_delivered'],
        ]);

        $action = $validated['action'];

        if ($action === 'start_work') {
            if ($jobCard->status !== 'pending') {
                return back()->with('error', 'Only pending jobs can be started.');
            }
            $jobCard->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);

            ActivityLog::log(
                action: 'started_work',
                description: "Started work on Job #{$jobCard->job_number} by ".Auth::user()->name.'.',
                module: 'Job Cards'
            );

            return back()->with('success', "Service started for Job #{$jobCard->job_number}.");
        }

        if ($action === 'complete_job') {
            if (! in_array($jobCard->status, ['pending', 'in_progress', 'additional_work'])) {
                return back()->with('error', 'Only pending or in-progress jobs can be completed.');
            }
            if ($jobCard->services()->count() === 0 && $jobCard->products()->count() === 0) {
                return back()->with('error', 'Cannot complete a job with no services or parts.');
            }

            // Task 5: Verify every billable service and product has a valid selling price
            $hasPendingServices = $jobCard->services()->where('is_price_pending', true)->exists();
            $hasPendingProducts = $jobCard->products()->where('is_price_pending', true)->exists();

            if ($hasPendingServices || $hasPendingProducts) {
                return back()->with('error', 'Some items do not have prices. Please assign prices before completing the job.');
            }

            try {
                $invoice = DB::transaction(function () use ($jobCard) {
                    $lockedJobCard = JobCard::where('id', $jobCard->id)->lockForUpdate()->firstOrFail();

                    // Recalculate service, product, discounts, and taxes
                    $lockedJobCard->recalculateTotals();

                    $sriLankaNow = now('Asia/Colombo');
                    $lockedJobCard->update([
                        'status' => 'completed',
                        'started_at' => $lockedJobCard->started_at ?? $sriLankaNow,
                        'completed_at' => $sriLankaNow,
                        'completed_by' => Auth::id(),
                    ]);

                    // Check if a billing record (Invoice) already exists for this job
                    $existingInvoice = Invoice::where('job_card_id', $lockedJobCard->id)
                        ->where('status', '!=', 'cancelled')
                        ->first();

                    if ($existingInvoice) {
                        if ($lockedJobCard->invoice_id !== $existingInvoice->id) {
                            $lockedJobCard->update(['invoice_id' => $existingInvoice->id]);
                        }

                        return $existingInvoice;
                    }

                    $grandTotal = (float) $lockedJobCard->grand_total;
                    $invoiceNumber = InvoiceNumberService::generateNextInvoiceNumber();

                    $customerSnapshot = [
                        'customer_name' => $lockedJobCard->customer->customer_name,
                        'phone' => $lockedJobCard->customer->phone,
                        'vehicle_number' => $lockedJobCard->vehicle->vehicle_number,
                        'vehicle_type' => $lockedJobCard->vehicleType?->name ?? 'Vehicle',
                        'current_mileage' => $lockedJobCard->current_mileage,
                        'next_service_mileage' => $lockedJobCard->next_service_mileage,
                    ];

                    $invoice = Invoice::create([
                        'invoice_number' => $invoiceNumber,
                        'branch_id' => $lockedJobCard->branch_id,
                        'invoice_date' => $sriLankaNow->toDateString(),
                        'due_date' => $sriLankaNow->toDateString(),
                        'customer_id' => $lockedJobCard->customer_id,
                        'job_card_id' => $lockedJobCard->id,
                        'invoice_type' => 'job_card',
                        'subject' => "Service Bill for {$lockedJobCard->vehicle->vehicle_number} (Job #{$lockedJobCard->job_number})",
                        'reference' => $lockedJobCard->job_number,
                        'subtotal' => $lockedJobCard->service_total + $lockedJobCard->product_total,
                        'discount_amount' => $lockedJobCard->discount_amount,
                        'tax_amount' => $lockedJobCard->tax_amount,
                        'grand_total' => $grandTotal,
                        'amount_paid' => 0.00,
                        'balance_amount' => $grandTotal,
                        'status' => 'issued',
                        'customer_snapshot' => $customerSnapshot,
                        'created_by' => Auth::id(),
                    ]);

                    // Add services as invoice items
                    foreach ($lockedJobCard->services as $serviceItem) {
                        InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'item_type' => 'service',
                            'item_id' => $serviceItem->service_id,
                            'item_code' => $serviceItem->service_code,
                            'item_name' => $serviceItem->service_name,
                            'quantity' => $serviceItem->quantity,
                            'unit_price' => $serviceItem->unit_price,
                            'line_total' => $serviceItem->line_total,
                            'notes' => $serviceItem->notes,
                        ]);
                    }

                    // Add products as invoice items
                    foreach ($lockedJobCard->products as $productItem) {
                        InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'item_type' => 'product',
                            'item_id' => $productItem->product_id,
                            'item_code' => $productItem->product_sku,
                            'item_name' => $productItem->product_name,
                            'quantity' => $productItem->quantity,
                            'unit_price' => $productItem->unit_price,
                            'unit_cost' => $productItem->unit_cost,
                            'line_total' => $productItem->line_total,
                            'notes' => $productItem->notes,
                        ]);
                    }

                    // Automatically apply any recorded job advances
                    $this->applyJobAdvancesToInvoice($lockedJobCard, $invoice, $grandTotal);

                    // Link invoice to job card
                    $lockedJobCard->update(['invoice_id' => $invoice->id]);

                    return $invoice;
                });

                ActivityLog::log(
                    action: 'completed_job',
                    description: "Completed Job #{$jobCard->job_number} and generated bill #{$invoice->invoice_number} by ".Auth::user()->name.'.',
                    module: 'Job Cards'
                );

                return back()->with('success', 'Job completed successfully. Invoice is ready for billing.');
            } catch (\Throwable $e) {
                return back()->with('error', 'Error completing job and generating bill: '.$e->getMessage());
            }
        }

        if ($action === 'mark_delivered') {
            if (! in_array($jobCard->status, ['completed', 'delivered'])) {
                return back()->with('error', 'Only completed jobs can be delivered.');
            }

            // Check payment policy
            $invoice = $jobCard->invoice;
            $customer = $jobCard->customer;
            $isFullyPaid = $invoice && ((float) $invoice->balance_amount <= 0.0001 || $invoice->status === 'paid');

            // Check company credit account
            $creditAccount = $customer?->creditAccount()->where('status', 'active')->first();
            $isCompanyCreditAllowed = $creditAccount && ! $creditAccount->isSuspended() && ! $creditAccount->isClosed() && ! $creditAccount->wouldExceedLimit(0);

            $overrideReason = trim((string) $request->input('delivery_override_reason', ''));

            if (! $isFullyPaid && ! $isCompanyCreditAllowed) {
                // If unpaid or partial, require admin override
                $user = Auth::user();
                if (! $user->isAdmin()) {
                    return back()->with('error', 'Full payment or approved company credit is required before vehicle delivery. Admin override required for exceptions.');
                }
                if ($overrideReason === '') {
                    return back()->with('error', 'An admin override reason is required to release an unpaid or partially paid vehicle.');
                }
            }

            $jobCard->update([
                'status' => 'delivered',
                'delivered_at' => now(),
            ]);

            $logDesc = "Vehicle {$jobCard->vehicle?->vehicle_number} delivered for Job #{$jobCard->job_number} by ".Auth::user()->name.'.';
            if ($overrideReason !== '') {
                $logDesc .= " Delivery Exception Override: {$overrideReason} (Approved by Admin: ".Auth::user()->name.')';
            }

            ActivityLog::log(
                action: 'delivered_vehicle',
                description: $logDesc,
                module: 'Job Cards'
            );

            return back()->with('success', "Vehicle handed over to customer. Job #{$jobCard->job_number} delivered.");
        }

        return back();
    }

    /**
     * Cancel a job card and reverse any stock deductions.
     */
    public function cancel(Request $request, JobCard $jobCard): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('cancel_jobs')) {
            abort(403, 'Unauthorized to cancel job cards.');
        }

        if ($jobCard->status === 'cancelled') {
            return back()->with('error', 'Job is already cancelled.');
        }

        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'min:3', 'max:500'],
        ]);

        try {
            DB::transaction(function () use ($jobCard, $validated, $user) {
                // Reverse all issued products stock
                foreach ($jobCard->products as $productItem) {
                    if ($productItem->is_stock_deducted && ! empty($productItem->batch_allocations)) {
                        $this->stockService->reverseStockFifo(
                            $productItem->batch_allocations,
                            $jobCard,
                            $user,
                            "Cancelled Job #{$jobCard->job_number}: {$validated['cancellation_reason']}"
                        );
                        $productItem->update(['is_stock_deducted' => false]);
                    }
                }

                // If invoice was created, mark it cancelled as well
                if ($jobCard->invoice) {
                    $jobCard->invoice->update([
                        'status' => 'cancelled',
                        'notes' => ($jobCard->invoice->notes ? $jobCard->invoice->notes."\n" : '')."Cancelled due to Job cancellation: {$validated['cancellation_reason']}",
                    ]);
                }

                $jobCard->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => $validated['cancellation_reason'],
                    'cancelled_by' => $user->id,
                    'cancelled_at' => now(),
                ]);
            });

            return redirect()->route('operations.job-cards.index')
                ->with('success', "Job #{$jobCard->job_number} cancelled and issued stock reversed to inventory.");
        } catch (Exception $e) {
            return back()->with('error', "Failed to cancel job: {$e->getMessage()}");
        }
    }

    /**
     * Generate an official Invoice from a Completed Job Card.
     */
    /**
     * Generate an official Invoice from a Completed Job Card.
     * Applies recorded advances, prevents duplicate invoices, and uses concurrency-safe numbering.
     */
    public function generateInvoice(JobCard $jobCard): RedirectResponse
    {
        if ($jobCard->status === 'cancelled') {
            return back()->with('error', 'Cannot generate invoice for a cancelled job.');
        }

        // 1. If invoice already exists on the job card, redirect to it
        if ($jobCard->invoice_id && $jobCard->invoice) {
            return redirect()->route('operations.invoices.show', $jobCard->invoice);
        }

        // 2. Check for any existing non-cancelled invoices referencing this job_card_id
        $existingInvoice = Invoice::where('job_card_id', $jobCard->id)
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existingInvoice) {
            if ($jobCard->invoice_id !== $existingInvoice->id) {
                $jobCard->update(['invoice_id' => $existingInvoice->id]);
            }

            return redirect()->route('operations.invoices.show', $existingInvoice)
                ->with('info', "Invoice #{$existingInvoice->invoice_number} is already linked to Job #{$jobCard->job_number}.");
        }

        // Credit Limit Validation (Stage 7)
        $customer = $jobCard->customer;
        $creditAccount = $customer?->creditAccount()->where('status', 'active')->first();
        if ($creditAccount && $creditAccount->wouldExceedLimit((float) $jobCard->grand_total)) {
            if (! Auth::user()->isAdmin() && ! request()->boolean('credit_override')) {
                return back()->with('error', 'Credit limit exceeded. Available credit: LKR '.number_format($creditAccount->available_credit, 2).', Job total: LKR '.number_format($jobCard->grand_total, 2).'. Admin override required.');
            }
        }

        try {
            return DB::transaction(function () use ($jobCard) {
                // Lock the job card row to prevent concurrent race conditions
                $lockedJobCard = JobCard::where('id', $jobCard->id)->lockForUpdate()->firstOrFail();

                if ($lockedJobCard->invoice_id && $lockedJobCard->invoice && $lockedJobCard->invoice->status !== 'cancelled') {
                    return redirect()->route('operations.invoices.show', $lockedJobCard->invoice);
                }

                $existingUnderLock = Invoice::where('job_card_id', $lockedJobCard->id)
                    ->where('status', '!=', 'cancelled')
                    ->first();

                if ($existingUnderLock) {
                    $lockedJobCard->update(['invoice_id' => $existingUnderLock->id]);

                    return redirect()->route('operations.invoices.show', $existingUnderLock);
                }

                $grandTotal = (float) $lockedJobCard->grand_total;
                $invoiceNumber = InvoiceNumberService::generateNextInvoiceNumber();

                $customerSnapshot = [
                    'customer_name' => $lockedJobCard->customer->customer_name,
                    'phone' => $lockedJobCard->customer->phone,
                    'vehicle_number' => $lockedJobCard->vehicle->vehicle_number,
                    'vehicle_type' => $lockedJobCard->vehicleType->name,
                    'current_mileage' => $lockedJobCard->current_mileage,
                    'next_service_mileage' => $lockedJobCard->next_service_mileage,
                ];

                $invoice = Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'branch_id' => $lockedJobCard->branch_id,
                    'invoice_date' => now()->toDateString(),
                    'due_date' => now()->toDateString(),
                    'customer_id' => $lockedJobCard->customer_id,
                    'job_card_id' => $lockedJobCard->id,
                    'invoice_type' => 'job_card',
                    'subject' => "Service Invoice for {$lockedJobCard->vehicle->vehicle_number} (Job #{$lockedJobCard->job_number})",
                    'reference' => $lockedJobCard->job_number,
                    'subtotal' => $lockedJobCard->service_total + $lockedJobCard->product_total,
                    'discount_amount' => $lockedJobCard->discount_amount,
                    'tax_amount' => $lockedJobCard->tax_amount,
                    'grand_total' => $grandTotal,
                    'amount_paid' => 0.00,
                    'balance_amount' => $grandTotal,
                    'status' => 'issued',
                    'customer_snapshot' => $customerSnapshot,
                    'created_by' => Auth::id(),
                ]);

                // Add services as invoice items
                foreach ($lockedJobCard->services as $serviceItem) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'item_type' => 'service',
                        'item_id' => $serviceItem->service_id,
                        'item_code' => $serviceItem->service_code,
                        'item_name' => $serviceItem->service_name,
                        'quantity' => $serviceItem->quantity,
                        'unit_price' => $serviceItem->unit_price,
                        'line_total' => $serviceItem->line_total,
                        'notes' => $serviceItem->notes,
                    ]);
                }

                // Add products as invoice items
                foreach ($lockedJobCard->products as $productItem) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'item_type' => 'product',
                        'item_id' => $productItem->product_id,
                        'item_code' => $productItem->product_sku,
                        'item_name' => $productItem->product_name,
                        'quantity' => $productItem->quantity,
                        'unit_price' => $productItem->unit_price,
                        'unit_cost' => $productItem->unit_cost,
                        'line_total' => $productItem->line_total,
                        'notes' => $productItem->notes,
                    ]);
                }

                // Automatically apply recorded advances to this final invoice
                $advanceSummary = $this->applyJobAdvancesToInvoice($lockedJobCard, $invoice, $grandTotal);

                // Link invoice to job card and items
                $lockedJobCard->update(['invoice_id' => $invoice->id]);
                $lockedJobCard->services()->update(['invoice_id' => $invoice->id]);
                $lockedJobCard->products()->update(['invoice_id' => $invoice->id]);
                $lockedJobCard->issues()->where('status', 'approved')->update(['status' => 'fixed']);

                $successMsg = "Invoice #{$invoice->invoice_number} generated for Job #{$lockedJobCard->job_number}.";
                if ($advanceSummary['applied_advance'] > 0) {
                    $successMsg .= ' LKR '.number_format($advanceSummary['applied_advance'], 2).' advance payment applied.';
                }
                if ($advanceSummary['excess_credit'] > 0) {
                    $successMsg .= ' LKR '.number_format($advanceSummary['excess_credit'], 2).' retained as unapplied customer credit.';
                }

                return redirect()->route('operations.invoices.show', $invoice)
                    ->with('success', $successMsg);
            });
        } catch (\Throwable $e) {
            Log::error("Failed to generate invoice for Job Card #{$jobCard->id}: ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Unable to generate invoice at this time: '.$e->getMessage());
        }
    }

    /**
     * Open the existing invoice associated with a completed job card.
     */
    public function goToInvoice(JobCard $jobCard): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('view_invoices')) {
            return back()->with('error', 'Unauthorized. Technician access to billing invoice details is restricted.');
        }

        if (! in_array($jobCard->status, ['completed', 'delivered'])) {
            return back()->with('error', 'Invoice is only accessible after job completion.');
        }

        $invoice = $jobCard->getResolvedInvoice();
        if (! $invoice) {
            return back()->with('error', 'Invoice not available for this completed job. Please contact the cashier or administrator.');
        }

        return redirect()->route('operations.invoices.show', $invoice);
    }

    /**
     * Reopen a completed or in-progress job for approved additional repair work.
     */
    public function startAdditionalWork(JobCard $jobCard): RedirectResponse
    {
        if (in_array($jobCard->status, ['delivered', 'cancelled'])) {
            return back()->with('error', 'Cannot add additional work to delivered or cancelled jobs. Please create a follow-up job card instead.');
        }

        // Must have at least one approved issue or allow opening
        $hasApprovedIssues = $jobCard->issues()->where('status', 'approved')->exists();
        if (! $hasApprovedIssues && $jobCard->status === 'completed') {
            return back()->with('error', 'Please record and approve a vehicle issue before starting additional work.');
        }

        $jobCard->update([
            'status' => 'additional_work',
        ]);

        return back()->with('success', "Job #{$jobCard->job_number} workflow changed to Additional Work In Progress. You can now add approved services and parts.");
    }

    /**
     * Mark additional repair work completed.
     */
    public function completeAdditionalWork(JobCard $jobCard): RedirectResponse
    {
        if ($jobCard->status !== 'additional_work') {
            return back()->with('error', 'Job is not currently in Additional Work status.');
        }

        $jobCard->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Mark any approved issues as fixed
        $jobCard->issues()->where('status', 'approved')->update([
            'status' => 'fixed',
        ]);

        $jobCard->recalculateTotals();

        return back()->with('success', "Additional work for Job #{$jobCard->job_number} completed. Ready for billing.");
    }

    /**
     * Generate a supplementary invoice for additional repair work on an already-invoiced job card.
     */
    public function generateSupplementaryInvoice(JobCard $jobCard): RedirectResponse
    {
        if (! $jobCard->invoice_id) {
            // If original invoice was never created, redirect to standard generate invoice
            return $this->generateInvoice($jobCard);
        }

        // Get unbilled services and products
        $unbilledServices = $jobCard->services()->whereNull('invoice_id')->get();
        $unbilledProducts = $jobCard->products()->whereNull('invoice_id')->get();

        if ($unbilledServices->isEmpty() && $unbilledProducts->isEmpty()) {
            return back()->with('error', 'No unbilled additional services or products found on this Job Card.');
        }

        return DB::transaction(function () use ($jobCard, $unbilledServices, $unbilledProducts) {
            $invoiceNumber = Invoice::generateInvoiceNumber();
            $subtotal = (float) $unbilledServices->sum('line_total') + (float) $unbilledProducts->sum('line_total');

            $customerSnapshot = [
                'customer_name' => $jobCard->customer->customer_name,
                'phone' => $jobCard->customer->phone,
                'vehicle_number' => $jobCard->vehicle->vehicle_number,
                'vehicle_type' => $jobCard->vehicleType->name,
                'current_mileage' => $jobCard->current_mileage,
                'next_service_mileage' => $jobCard->next_service_mileage,
                'is_supplementary' => true,
                'parent_invoice' => $jobCard->invoice?->invoice_number,
            ];

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'branch_id' => $jobCard->branch_id,
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->toDateString(),
                'customer_id' => $jobCard->customer_id,
                'job_card_id' => $jobCard->id,
                'invoice_type' => 'job_card',
                'subject' => "Supplementary Invoice – Additional Work for {$jobCard->vehicle->vehicle_number} (Job #{$jobCard->job_number})",
                'reference' => "{$jobCard->job_number}-SUP",
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'grand_total' => $subtotal,
                'amount_paid' => 0,
                'balance_amount' => $subtotal,
                'status' => 'issued',
                'customer_snapshot' => $customerSnapshot,
                'notes' => "Supplementary billing for additional repair work on Job #{$jobCard->job_number} (Original Invoice #{$jobCard->invoice?->invoice_number})",
                'created_by' => Auth::id(),
            ]);

            // Add unbilled services
            foreach ($unbilledServices as $serviceItem) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => 'service',
                    'item_id' => $serviceItem->service_id,
                    'item_code' => $serviceItem->service_code,
                    'item_name' => $serviceItem->service_name.' (Additional Work)',
                    'quantity' => $serviceItem->quantity,
                    'unit_price' => $serviceItem->unit_price,
                    'line_total' => $serviceItem->line_total,
                    'notes' => $serviceItem->notes,
                ]);

                $serviceItem->update(['invoice_id' => $invoice->id]);
            }

            // Add unbilled products
            foreach ($unbilledProducts as $productItem) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => 'product',
                    'item_id' => $productItem->product_id,
                    'item_code' => $productItem->product_sku,
                    'item_name' => $productItem->product_name.' (Additional Work)',
                    'quantity' => $productItem->quantity,
                    'unit_price' => $productItem->unit_price,
                    'unit_cost' => $productItem->unit_cost,
                    'line_total' => $productItem->line_total,
                    'notes' => $productItem->notes,
                ]);

                $productItem->update(['invoice_id' => $invoice->id]);
            }

            // Mark approved issues as fixed
            $jobCard->issues()->where('status', 'approved')->update([
                'status' => 'fixed',
            ]);

            return redirect()->route('operations.invoices.show', $invoice)
                ->with('success', "Supplementary Invoice #{$invoice->invoice_number} generated for additional repair work.");
        });
    }

    /**
     * Create a linked follow-up Job Card for a delivered vehicle returning on another date.
     */
    public function createFollowUp(JobCard $jobCard): RedirectResponse
    {
        $newJob = JobCard::create([
            'job_number' => JobCard::generateJobNumber(),
            'branch_id' => $jobCard->branch_id,
            'customer_id' => $jobCard->customer_id,
            'vehicle_id' => $jobCard->vehicle_id,
            'vehicle_type_id' => $jobCard->vehicle_type_id,
            'date' => now()->toDateString(),
            'current_mileage' => $jobCard->vehicle->current_mileage,
            'next_service_mileage' => $jobCard->vehicle->next_service_mileage,
            'service_notes' => "Follow-up visit for previous Job #{$jobCard->job_number}",
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('operations.job-cards.show', $newJob)
            ->with('success', "Follow-up Job Card #{$newJob->job_number} created for {$jobCard->vehicle->vehicle_number}.");
    }

    /**
     * Printable view of Job Card (Full or Selected Items).
     * Pure read-only operation: no inventory movement, no invoice generation, no status change.
     */
    public function print(Request $request, JobCard $jobCard): View|RedirectResponse
    {
        $user = Auth::user();

        // 1. Branch authorization check
        if ($jobCard->branch_id && ! $user->isAdmin()) {
            $accessible = BranchContext::getAccessibleBranches($user)->pluck('id')->toArray();
            if (! in_array($jobCard->branch_id, $accessible)) {
                abort(403, 'Unauthorized to view or print records from another branch.');
            }
        }

        // 2. Cancelled job authorization check
        if ($jobCard->status === 'cancelled') {
            if (! $user->isAdmin() && ! $user->hasPermission('cancel_jobs')) {
                abort(403, 'Unauthorized to view or print cancelled job cards.');
            }
        }

        // 3. Determine if printing selected items
        $isSelective = $request->boolean('selected') || $request->has('services') || $request->has('products') || $request->has('issues') || $request->has('carpets');
        $selectedServiceIds = $request->input('services', []);
        $selectedProductIds = $request->input('products', []);
        $selectedIssueIds = $request->input('issues', []);
        $includeCarpets = $request->boolean('carpets');

        if (is_string($selectedServiceIds)) {
            $selectedServiceIds = array_filter(array_map('trim', explode(',', $selectedServiceIds)));
        }
        if (is_string($selectedProductIds)) {
            $selectedProductIds = array_filter(array_map('trim', explode(',', $selectedProductIds)));
        }
        if (is_string($selectedIssueIds)) {
            $selectedIssueIds = array_filter(array_map('trim', explode(',', $selectedIssueIds)));
        }

        if ($isSelective && empty($selectedServiceIds) && empty($selectedProductIds) && empty($selectedIssueIds) && ! $includeCarpets) {
            return back()->with('error', 'Please select at least one item to print.');
        }

        // 4. Load relationships safely
        $jobCard->load([
            'customer',
            'vehicle.vehicleType',
            'vehicleType',
            'assignedTechnician',
            'creator',
            'branch',
            'issues.recordedByUser',
            'invoice.payments',
            'carpets',
        ]);

        $services = $jobCard->services;
        $products = $jobCard->products;
        $issues = $jobCard->issues;
        $carpets = $jobCard->carpets;

        if ($isSelective) {
            $services = $services->whereIn('id', $selectedServiceIds);
            $products = $products->whereIn('id', $selectedProductIds);
            $issues = $issues->whereIn('id', $selectedIssueIds);
            if (! $includeCarpets) {
                $carpets = collect();
            }

            if ($services->isEmpty() && $products->isEmpty() && $issues->isEmpty() && $carpets->isEmpty()) {
                return back()->with('error', 'Please select at least one item to print.');
            }
        }

        $company = CompanySetting::getSettings();

        // Calculate totals for print view (never exposing FIFO costs)
        $servicesTotal = (float) $services->sum('line_total');
        $productsTotal = (float) $products->sum('line_total');
        $grandTotal = $isSelective
            ? ($servicesTotal + $productsTotal)
            : (float) $jobCard->grand_total;

        return view('operations.jobs.print', compact(
            'jobCard',
            'company',
            'services',
            'products',
            'issues',
            'carpets',
            'isSelective',
            'includeCarpets',
            'servicesTotal',
            'productsTotal',
            'grandTotal',
            'selectedServiceIds',
            'selectedProductIds',
            'selectedIssueIds'
        ));
    }

    /**
     * Download Job Card as PDF (Full or Selected Items).
     */
    public function downloadPdf(Request $request, JobCard $jobCard)
    {
        $user = Auth::user();

        // 1. Branch authorization check
        if ($jobCard->branch_id && ! $user->isAdmin()) {
            $accessible = BranchContext::getAccessibleBranches($user)->pluck('id')->toArray();
            if (! in_array($jobCard->branch_id, $accessible)) {
                abort(403, 'Unauthorized to view or download records from another branch.');
            }
        }

        // 2. Cancelled job authorization check
        if ($jobCard->status === 'cancelled') {
            if (! $user->isAdmin() && ! $user->hasPermission('cancel_jobs')) {
                abort(403, 'Unauthorized to view or download cancelled job cards.');
            }
        }

        // 3. Determine if printing selected items
        $isSelective = $request->boolean('selected') || $request->has('services') || $request->has('products') || $request->has('issues') || $request->has('carpets');
        $selectedServiceIds = $request->input('services', []);
        $selectedProductIds = $request->input('products', []);
        $selectedIssueIds = $request->input('issues', []);
        $includeCarpets = $request->boolean('carpets');

        if (is_string($selectedServiceIds)) {
            $selectedServiceIds = array_filter(array_map('trim', explode(',', $selectedServiceIds)));
        }
        if (is_string($selectedProductIds)) {
            $selectedProductIds = array_filter(array_map('trim', explode(',', $selectedProductIds)));
        }
        if (is_string($selectedIssueIds)) {
            $selectedIssueIds = array_filter(array_map('trim', explode(',', $selectedIssueIds)));
        }

        if ($isSelective && empty($selectedServiceIds) && empty($selectedProductIds) && empty($selectedIssueIds) && ! $includeCarpets) {
            return back()->with('error', 'Please select at least one item to print.');
        }

        $jobCard->load([
            'customer',
            'vehicle.vehicleType',
            'vehicleType',
            'assignedTechnician',
            'creator',
            'branch',
            'issues.recordedByUser',
            'invoice.payments',
            'carpets',
        ]);

        $services = $jobCard->services;
        $products = $jobCard->products;
        $issues = $jobCard->issues;
        $carpets = $jobCard->carpets;

        if ($isSelective) {
            $services = $services->whereIn('id', $selectedServiceIds);
            $products = $products->whereIn('id', $selectedProductIds);
            $issues = $issues->whereIn('id', $selectedIssueIds);
            if (! $includeCarpets) {
                $carpets = collect();
            }

            if ($services->isEmpty() && $products->isEmpty() && $issues->isEmpty() && $carpets->isEmpty()) {
                return back()->with('error', 'Please select at least one item to print.');
            }
        }

        $company = CompanySetting::getSettings();
        $servicesTotal = (float) $services->sum('line_total');
        $productsTotal = (float) $products->sum('line_total');
        $grandTotal = $isSelective
            ? ($servicesTotal + $productsTotal)
            : (float) $jobCard->grand_total;

        $pdf = Pdf::loadView('operations.jobs.pdf', compact(
            'jobCard',
            'company',
            'services',
            'products',
            'issues',
            'carpets',
            'isSelective',
            'includeCarpets',
            'servicesTotal',
            'productsTotal',
            'grandTotal',
            'selectedServiceIds',
            'selectedProductIds',
            'selectedIssueIds'
        ));
        $pdf->setPaper('a4', 'portrait');

        $suffix = $isSelective ? '_Selected' : '_Full';

        return $pdf->download("JobCard_{$jobCard->job_number}{$suffix}.pdf");
    }

    /**
     * Record a payment (advance, partial, or final) directly from Job Card.
     */
    public function recordPayment(Request $request, JobCard $jobCard): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isAdmin() && ! $user->hasPermission('record_payments')) {
            abort(403, 'Unauthorized. Technicians cannot record customer payments. Payments must be processed through the Cashier Billing workflow.');
        }

        if ($jobCard->branch_id && ! $user->canAccessBranch($jobCard->branch_id)) {
            abort(403, 'Unauthorized access to this branch.');
        }

        if ($jobCard->status === 'cancelled') {
            return back()->with('error', 'Cannot record payments for cancelled jobs.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,card,bank_transfer,cheque,credit'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $amount = round((float) $validated['amount'], 2);
        $method = $validated['payment_method'];

        // Handle Approved Company Credit validation
        if ($method === 'credit') {
            $customer = $jobCard->customer;
            $creditAccount = $customer?->creditAccount()->where('status', 'active')->first();

            if (! $creditAccount) {
                return back()->with('error', 'Customer does not have an active approved credit account.');
            }

            if ($creditAccount->wouldExceedLimit($amount) && ! $user->isAdmin()) {
                return back()->with('error', 'Company credit limit exceeded. Available credit: LKR '.number_format($creditAccount->available_credit, 2).'. Admin override required.');
            }
        }

        try {
            $payment = DB::transaction(function () use ($jobCard, $validated, $amount, $user) {
                $lockedJobCard = JobCard::where('id', $jobCard->id)->lockForUpdate()->firstOrFail();
                $paymentNumber = Payment::generatePaymentNumber();

                if ($lockedJobCard->invoice_id && $lockedJobCard->invoice) {
                    // Payment against already issued final invoice
                    $invoice = Invoice::where('id', $lockedJobCard->invoice_id)->lockForUpdate()->firstOrFail();
                    $currentBalance = round((float) $invoice->balance_amount, 2);

                    if ($currentBalance <= 0.0001) {
                        throw new Exception("Invoice #{$invoice->invoice_number} is already paid in full.");
                    }

                    if ($amount > $currentBalance) {
                        throw new Exception('Payment amount (LKR '.number_format($amount, 2).') cannot exceed invoice remaining balance of LKR '.number_format($currentBalance, 2).'.');
                    }

                    $payment = Payment::create([
                        'invoice_id' => $invoice->id,
                        'job_card_id' => $lockedJobCard->id,
                        'customer_id' => $lockedJobCard->customer_id,
                        'payment_number' => $paymentNumber,
                        'payment_date' => $validated['payment_date'],
                        'amount' => $amount,
                        'payment_method' => $validated['payment_method'],
                        'reference_number' => $validated['reference_number'] ?? null,
                        'notes' => $validated['notes'] ?? null,
                        'is_advance' => false,
                        'status' => 'completed',
                        'created_by' => $user->id,
                    ]);

                    // Recalculate invoice totals
                    $totalPaid = (float) Payment::where('invoice_id', $invoice->id)->where('status', 'completed')->sum('amount');
                    $totalPaid = round($totalPaid, 2);
                    $grandTotal = round((float) $invoice->grand_total, 2);
                    $newBalance = max(0, round($grandTotal - $totalPaid, 2));

                    $newStatus = ($newBalance <= 0.0001 || $totalPaid >= $grandTotal)
                        ? 'paid'
                        : ($totalPaid > 0 ? 'partially_paid' : 'issued');

                    $invoice->update([
                        'amount_paid' => $totalPaid,
                        'balance_amount' => $newBalance,
                        'status' => $newStatus,
                    ]);
                } else {
                    // Advance payment before final invoice is generated
                    $existingAdvances = (float) Payment::where('job_card_id', $lockedJobCard->id)
                        ->whereNull('invoice_id')
                        ->where('status', 'completed')
                        ->sum('amount');
                    $estimatedTotal = (float) $lockedJobCard->grand_total;
                    if ($estimatedTotal > 0) {
                        $remaining = max(0, round($estimatedTotal - $existingAdvances, 2));
                        if ($amount > $remaining) {
                            throw new Exception('Advance payment amount (LKR '.number_format($amount, 2).') cannot exceed the remaining estimated job balance of LKR '.number_format($remaining, 2).'.');
                        }
                    }

                    $payment = Payment::create([
                        'invoice_id' => null,
                        'job_card_id' => $lockedJobCard->id,
                        'customer_id' => $lockedJobCard->customer_id,
                        'payment_number' => $paymentNumber,
                        'payment_date' => $validated['payment_date'],
                        'amount' => $amount,
                        'payment_method' => $validated['payment_method'],
                        'reference_number' => $validated['reference_number'] ?? null,
                        'notes' => $validated['notes'] ?? null,
                        'is_advance' => true,
                        'status' => 'completed',
                        'created_by' => $user->id,
                    ]);
                }

                ActivityLog::log(
                    action: 'payment_recorded',
                    description: "Recorded {$validated['payment_method']} payment #{$payment->payment_number} of LKR ".number_format($amount, 2)." for Job Card #{$lockedJobCard->job_number}.",
                    module: 'Operations',
                    userId: $user->id
                );

                return $payment;
            });

            return back()->with('success', 'Payment of LKR '.number_format($amount, 2)." recorded successfully (Receipt #{$payment->payment_number}).")
                ->with('recent_payment_id', $payment->id);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Print 80mm Sales Bill directly from Job Card:
     * Generated directly from Job Card without requiring invoice or prior payment.
     * Shows billable services/products, subtotal, discount, grand total, payments received, and balance.
     * Does not create an invoice database record.
     */
    public function printBill(JobCard $jobCard): View|RedirectResponse
    {
        $user = Auth::user();
        if ($jobCard->branch_id && ! $user->isAdmin()) {
            $accessible = BranchContext::getAccessibleBranches($user)->pluck('id')->toArray();
            if (! in_array($jobCard->branch_id, $accessible)) {
                abort(403, 'Unauthorized access to this branch.');
            }
        }

        $jobCard->load([
            'customer',
            'vehicle',
            'vehicleType',
            'branch',
            'creator',
            'services.service',
            'products.product',
            'payments' => function ($q) {
                $q->where('status', 'completed')->orderBy('payment_date', 'asc');
            },
        ]);

        $hasItems = ($jobCard->services->count() > 0 || $jobCard->products->count() > 0);
        if (! $hasItems) {
            return back()->with('error', 'Cannot print bill: No billable services or products have been added to this Job Card.');
        }

        $company = CompanySetting::getSettings();

        $serviceTotal = (float) $jobCard->service_total;
        $productTotal = (float) $jobCard->product_total;
        $subtotal = round($serviceTotal + $productTotal, 2);
        $discount = (float) $jobCard->discount_amount;
        $tax = (float) $jobCard->tax_amount;
        $grandTotal = (float) $jobCard->grand_total;

        // Cumulative payments for this Job Card
        $totalPaid = (float) Payment::where(function ($q) use ($jobCard) {
            $q->where('job_card_id', $jobCard->id);
            if ($jobCard->invoice_id) {
                $q->orWhere('invoice_id', $jobCard->invoice_id);
            }
        })->where('status', 'completed')->sum('amount');

        $totalPaid = round($totalPaid, 2);
        $balance = max(0, round($grandTotal - $totalPaid, 2));

        if ($totalPaid <= 0.0001) {
            $paymentStatus = 'Unpaid';
        } elseif ($balance <= 0.0001) {
            $paymentStatus = 'Paid in Full';
        } else {
            $paymentStatus = 'Partially Paid';
        }

        return view('operations.jobs.bill', compact(
            'jobCard',
            'company',
            'subtotal',
            'discount',
            'tax',
            'grandTotal',
            'totalPaid',
            'balance',
            'paymentStatus'
        ));
    }

    /**
     * Render 80mm thermal Advance Payment Receipt.
     */
    public function advanceReceipt(JobCard $jobCard): View
    {
        $jobCard->load([
            'customer',
            'vehicle',
            'vehicleType',
            'branch',
            'payments' => function ($q) {
                $q->where('status', 'completed')->orderBy('payment_date', 'asc');
            },
        ]);

        $company = CompanySetting::getSettings();
        $totalAdvance = (float) $jobCard->payments()->where('status', 'completed')->sum('amount');
        $estimatedTotal = (float) $jobCard->grand_total;
        $estimatedBalance = max(0, round($estimatedTotal - $totalAdvance, 2));

        return view('operations.jobs.advance-receipt', compact(
            'jobCard',
            'company',
            'totalAdvance',
            'estimatedTotal',
            'estimatedBalance'
        ));
    }

    /**
     * Manage completed Job Card: Option 1 (Permanently Delete) or Option 2 (Recreate Job for Editing).
     */
    public function destroy(Request $request, JobCard $jobCard): RedirectResponse
    {
        $user = $request->user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Unauthorized. Only administrators can delete Job Cards.');
        }

        return $this->manageCompletedJob($request, $jobCard);
    }

    /**
     * Manage completed Job Card: Process either permanent deletion or reopening for editing.
     */
    public function manageCompletedJob(Request $request, JobCard $jobCard): RedirectResponse
    {
        $user = $request->user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Unauthorized. Only administrators can delete or recreate Job Cards.');
        }

        $validated = $request->validate([
            'manage_action' => ['nullable', 'string', Rule::in(['permanently_delete', 'recreate_for_editing'])],
            'reason' => [
                Rule::requiredIf(fn () => $request->input('manage_action', 'permanently_delete') === 'permanently_delete'),
                'nullable',
                'string',
                Rule::in([
                    'Incorrect services added',
                    'Incorrect products added',
                    'Wrong vehicle selected',
                    'Incorrect pricing',
                    'Customer requested cancellation',
                    'Duplicate Job Card',
                    'Other',
                ]),
            ],
            'explanation' => ['nullable', 'string', 'max:1000'],
        ], [
            'reason.required' => 'Please select a reason for deletion.',
        ]);

        $action = $validated['manage_action'] ?? 'permanently_delete';
        $reason = $validated['reason'] ?? ($action === 'recreate_for_editing' ? 'Job reopened for editing' : '');
        $explanation = trim((string) ($validated['explanation'] ?? ''));

        if ($action === 'recreate_for_editing') {
            return $this->reopenJobForEditing($request, $jobCard, $reason, $explanation, $user);
        }

        return $this->permanentlyDeleteJob($request, $jobCard, $reason, $explanation, $user);
    }

    /**
     * Automatically allocate available unallocated advances from the Job Card to the newly generated invoice.
     * If total advances exceed the new invoice grand total (Example B), the excess amount is preserved
     * as an unapplied customer credit balance (not tied to this invoice or job card) without issuing any physical refund.
     *
     * @return array{applied_advance: float, remaining_balance: float, excess_credit: float, payment_status: string}
     */
    protected function applyJobAdvancesToInvoice(JobCard $jobCard, Invoice $invoice, float $grandTotal): array
    {
        $advances = Payment::where('job_card_id', $jobCard->id)
            ->whereNull('invoice_id')
            ->where('status', 'completed')
            ->orderBy('id', 'asc')
            ->lockForUpdate()
            ->get();

        $totalAdvances = round((float) $advances->sum('amount'), 2);
        $appliedAdvance = min($grandTotal, $totalAdvances);
        $excessCredit = max(0, round($totalAdvances - $grandTotal, 2));
        $remainingBalance = max(0, round($grandTotal - $appliedAdvance, 2));

        $remainingToApply = $appliedAdvance;

        foreach ($advances as $adv) {
            if ($remainingToApply <= 0.0001) {
                // Unapplied excess credit: detach from job card to become general customer credit balance
                $adv->update([
                    'invoice_id' => null,
                    'job_card_id' => null,
                    'customer_id' => $jobCard->customer_id,
                    'is_advance' => true,
                    'status' => 'completed',
                    'notes' => trim(($adv->notes ?? '')." [Unapplied customer credit - excess from Job #{$jobCard->job_number}]"),
                ]);

                continue;
            }

            $advAmt = (float) $adv->amount;

            if ($advAmt <= $remainingToApply + 0.0001) {
                $adv->update([
                    'invoice_id' => $invoice->id,
                    'job_card_id' => $jobCard->id,
                    'is_advance' => true,
                    'status' => 'completed',
                    'notes' => trim(($adv->notes ?? '')." [Applied to Invoice #{$invoice->invoice_number}]"),
                ]);
                $remainingToApply = max(0, round($remainingToApply - $advAmt, 2));
            } else {
                // Split payment: part covers the remainder of this invoice, part remains unapplied customer credit
                $applyAmt = $remainingToApply;
                $excessAmt = round($advAmt - $applyAmt, 2);
                $origPaymentNumber = $adv->payment_number;

                $adv->update([
                    'amount' => $applyAmt,
                    'invoice_id' => $invoice->id,
                    'job_card_id' => $jobCard->id,
                    'is_advance' => true,
                    'status' => 'completed',
                    'notes' => trim(($adv->notes ?? '').' [Applied LKR '.number_format($applyAmt, 2)." to Invoice #{$invoice->invoice_number} (Split from #{$origPaymentNumber})]"),
                ]);

                Payment::create([
                    'invoice_id' => null,
                    'job_card_id' => null,
                    'customer_id' => $jobCard->customer_id,
                    'payment_receipt_id' => $adv->payment_receipt_id,
                    'payment_number' => Payment::generatePaymentNumber(),
                    'payment_date' => $adv->payment_date,
                    'amount' => $excessAmt,
                    'payment_method' => $adv->payment_method,
                    'reference_number' => $adv->reference_number ?? $origPaymentNumber,
                    'notes' => 'Unapplied customer credit (Excess of LKR '.number_format($excessAmt, 2)." from Job #{$jobCard->job_number} after Invoice #{$invoice->invoice_number}. Orig Payment #{$origPaymentNumber})",
                    'is_advance' => true,
                    'status' => 'completed',
                    'created_by' => $adv->created_by,
                ]);

                $remainingToApply = 0.00;
            }
        }

        $paymentStatus = ($remainingBalance <= 0.0001)
            ? 'paid'
            : ($appliedAdvance > 0 ? 'partially_paid' : 'issued');

        $invoice->update([
            'amount_paid' => $appliedAdvance,
            'balance_amount' => $remainingBalance,
            'status' => $paymentStatus,
        ]);

        return [
            'applied_advance' => $appliedAdvance,
            'remaining_balance' => $remainingBalance,
            'excess_credit' => $excessCredit,
            'payment_status' => $paymentStatus,
        ];
    }

    /**
     * OPTION 2: Recreate Job for Editing.
     * Reopens the SAME Job Card with the SAME ID and Job Number, removes the previous invoice from active billing,
     * retains existing products/services, and sets status back to pending.
     * Automatically transfers valid paid amounts into a job advance without requiring manual cashier reversals.
     */
    protected function reopenJobForEditing(Request $request, JobCard $jobCard, string $reason, string $explanation, User $user): RedirectResponse
    {
        if (! $user->isAdmin()) {
            abort(403, 'Unauthorized. Only administrators can recreate Job Cards for editing.');
        }

        try {
            DB::beginTransaction();

            $lockedJobCard = JobCard::where('id', $jobCard->id)->lockForUpdate()->firstOrFail();

            if (! in_array($lockedJobCard->status, ['completed', 'delivered', 'cancelled'])) {
                DB::rollBack();

                return back()->with('error', 'Only completed, delivered, or cancelled jobs can be reopened for editing.');
            }

            // Identify all linked invoices
            $linkedInvoices = Invoice::where('job_card_id', $lockedJobCard->id)
                ->when($lockedJobCard->invoice_id, fn ($q) => $q->orWhere('id', $lockedJobCard->invoice_id))
                ->lockForUpdate()
                ->get()
                ->unique('id');

            $invoiceIds = $linkedInvoices->pluck('id')->filter()->all();

            $primaryInvoice = $lockedJobCard->getResolvedInvoice() ?? $linkedInvoices->first();
            $adminName = $user->name;
            $reopenedTimestamp = now('Asia/Colombo')->format('Y-m-d H:i:s');
            $prevStatus = $lockedJobCard->status;
            $jobNumber = $lockedJobCard->job_number;
            $oldInvoiceNumber = $primaryInvoice?->invoice_number ?? 'None';

            // Find all valid (non-cancelled, non-reversed) recorded payments associated with linked invoices or this job card
            $validPayments = Payment::where(function ($query) use ($invoiceIds, $lockedJobCard) {
                if (! empty($invoiceIds)) {
                    $query->whereIn('invoice_id', $invoiceIds);
                }
                $query->orWhere('job_card_id', $lockedJobCard->id);
            })
                ->whereNotIn('status', ['cancelled', 'reversed'])
                ->lockForUpdate()
                ->get();

            // Automatically transfer valid payments to job advance
            $totalAdvancePreserved = 0.0;
            foreach ($validPayments as $payment) {
                $origInvoiceNum = $payment->invoice?->invoice_number ?? ($payment->invoice_id ? "INV#{$payment->invoice_id}" : 'None');
                $noteAppend = "[Transferred to Job Advance upon reopening Job #{$jobNumber}. Orig Invoice: {$origInvoiceNum}]";

                $payment->update([
                    'invoice_id' => null,
                    'job_card_id' => $lockedJobCard->id,
                    'is_advance' => true,
                    'status' => 'completed',
                    'notes' => trim(($payment->notes ?? '').' '.$noteAppend),
                ]);

                $totalAdvancePreserved += (float) $payment->amount;
            }

            // Update payment allocations linked to the voided invoices
            if (! empty($invoiceIds)) {
                PaymentAllocation::whereIn('invoice_id', $invoiceIds)
                    ->update([
                        'notes' => DB::raw("CONCAT(COALESCE(notes, ''), ' [Invoice voided: Job #{$jobNumber} reopened]')"),
                    ]);
            }

            // Void old invoices and remove them from active receivables and sales
            foreach ($linkedInvoices as $inv) {
                $inv->update([
                    'status' => 'cancelled',
                    'amount_paid' => 0.00,
                    'balance_amount' => 0.00,
                    'job_card_id' => null,
                    'cancellation_reason' => "Job Card #{$jobNumber} reopened for editing by {$adminName}.",
                    'cancelled_by' => $user->id,
                    'cancelled_at' => now(),
                    'reference' => "{$inv->reference} (Reopened)",
                    'notes' => trim(($inv->notes ?? '')."\nVoided/Archived: Job Card #{$jobNumber} reopened for editing by {$adminName} on {$reopenedTimestamp}."),
                ]);
            }

            // Clear existing invoice_id on Job Card and services/products
            $lockedJobCard->update([
                'status' => 'pending',
                'completed_at' => null,
                'completed_by' => null,
                'invoice_id' => null,
            ]);

            $lockedJobCard->services()->update(['invoice_id' => null]);
            $lockedJobCard->products()->update(['invoice_id' => null]);

            // Ensure totals reflect retained services and products
            $lockedJobCard->recalculateTotals();

            // Activity Log
            ActivityLog::log(
                action: 'reopened_job_for_editing',
                description: "{$jobNumber} reopened for editing by {$adminName}. Previous invoice {$oldInvoiceNumber} voided and removed from active billing. LKR ".number_format($totalAdvancePreserved, 2).' preserved as job advance.',
                module: 'Job Cards',
                userId: $user->id
            );

            DB::commit();

            $successMsg = "Job {$jobNumber} reopened successfully. Previous services and products are available for editing. The old invoice has been removed from active billing.";
            if ($totalAdvancePreserved > 0) {
                $successMsg .= ' LKR '.number_format($totalAdvancePreserved, 2).' previous payment preserved as job advance.';
            }

            return redirect()->route('operations.job-cards.show', $lockedJobCard->id)
                ->with('success', $successMsg);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Failed to reopen Job Card #{$jobCard->job_number} for editing: {$e->getMessage()}", [
                'job_card_id' => $jobCard->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Error reopening job for editing: '.$e->getMessage());
        }
    }

    /**
     * OPTION 1: Permanently Delete Job.
     * Permanently deletes original Job Card, reverses FIFO stock, cancels eligible linked invoices,
     * and moves valid payments into unapplied customer credit balance.
     */
    protected function permanentlyDeleteJob(Request $request, JobCard $jobCard, string $reason, string $explanation, User $user): RedirectResponse
    {
        if (! $user->isAdmin()) {
            abort(403, 'Unauthorized. Only administrators can permanently delete Job Cards.');
        }

        try {
            DB::beginTransaction();

            $lockedJobCard = JobCard::where('id', $jobCard->id)->lockForUpdate()->firstOrFail();

            // Find all linked invoices (primary and supplementary)
            $linkedInvoices = Invoice::where('job_card_id', $lockedJobCard->id)
                ->when($lockedJobCard->invoice_id, fn ($q) => $q->orWhere('id', $lockedJobCard->invoice_id))
                ->lockForUpdate()
                ->get()
                ->unique('id');

            $invoiceIds = $linkedInvoices->pluck('id')->filter()->all();

            $primaryInvoice = $lockedJobCard->getResolvedInvoice() ?? $linkedInvoices->first();
            $adminName = $user->name;
            $deletionTimestamp = now('Asia/Colombo')->format('Y-m-d H:i:s');
            $jobNumber = $lockedJobCard->job_number;

            // Find all valid (non-cancelled, non-reversed) recorded payments associated with linked invoices or this job card
            $validPayments = Payment::where(function ($query) use ($invoiceIds, $lockedJobCard) {
                if (! empty($invoiceIds)) {
                    $query->whereIn('invoice_id', $invoiceIds);
                }
                $query->orWhere('job_card_id', $lockedJobCard->id);
            })
                ->whereNotIn('status', ['cancelled', 'reversed'])
                ->lockForUpdate()
                ->get();

            // Move valid unapplied amounts into the customer's credit balance using a properly recorded accounting adjustment
            $totalCreditPreserved = 0.0;
            foreach ($validPayments as $payment) {
                $origInvoiceNum = $payment->invoice?->invoice_number ?? ($payment->invoice_id ? "INV#{$payment->invoice_id}" : 'None');
                $noteAppend = "[Retained as unapplied customer credit upon deletion of Job #{$jobNumber}. Orig Invoice: {$origInvoiceNum}. Reason: {$reason}]";

                $payment->update([
                    'invoice_id' => null,
                    'job_card_id' => null,
                    'customer_id' => $lockedJobCard->customer_id,
                    'is_advance' => true,
                    'status' => 'completed',
                    'notes' => trim(($payment->notes ?? '').' '.$noteAppend),
                ]);

                $totalCreditPreserved += (float) $payment->amount;
            }

            // Update payment allocations linked to the voided invoices
            if (! empty($invoiceIds)) {
                PaymentAllocation::whereIn('invoice_id', $invoiceIds)
                    ->update([
                        'notes' => DB::raw("CONCAT(COALESCE(notes, ''), ' [Job #{$jobNumber} permanently deleted]')"),
                    ]);
            }

            // FIFO Inventory and Stock Correction:
            $jobProducts = $lockedJobCard->products()->lockForUpdate()->get();
            $reversedBatches = [];

            foreach ($jobProducts as $prodItem) {
                if ($prodItem->is_stock_deducted && ! empty($prodItem->batch_allocations)) {
                    try {
                        $this->stockService->reverseStockFifo(
                            $prodItem->batch_allocations,
                            $lockedJobCard,
                            $user,
                            "Job Card #{$lockedJobCard->job_number} permanent deletion. Reason: {$reason}"
                        );
                        $reversedBatches[] = [
                            'product_id' => $prodItem->product_id,
                            'product_name' => $prodItem->product_name,
                            'allocations' => $prodItem->batch_allocations,
                        ];
                    } catch (\Throwable $stockEx) {
                        DB::rollBack();
                        Log::error("Stock correction failed while deleting Job Card #{$lockedJobCard->job_number}: {$stockEx->getMessage()}", [
                            'job_id' => $lockedJobCard->id,
                            'product_item_id' => $prodItem->id,
                        ]);

                        return back()->with('error', 'Unable to delete this job because the associated stock transactions could not be safely corrected.');
                    }
                }
            }

            // Save Permanent Deletion Audit Log
            $auditPayload = [
                'job_id' => $lockedJobCard->id,
                'job_number' => $lockedJobCard->job_number,
                'vehicle_number' => $lockedJobCard->vehicle?->vehicle_number ?? 'N/A',
                'customer_id' => $lockedJobCard->customer_id,
                'customer_name' => $lockedJobCard->customer?->customer_name ?? 'N/A',
                'job_total' => (float) $lockedJobCard->grand_total,
                'invoice_id' => $primaryInvoice?->id,
                'invoice_number' => $primaryInvoice?->invoice_number ?? 'N/A',
                'invoice_payment_status' => $primaryInvoice?->payment_status ?? 'Unpaid',
                'customer_credit_preserved' => $totalCreditPreserved,
                'deletion_reason' => $reason,
                'additional_explanation' => $explanation ?: null,
                'admin_id' => $user->id,
                'admin_name' => $adminName,
                'deletion_timestamp' => $deletionTimestamp,
                'reversed_inventory' => $reversedBatches,
            ];

            ActivityLog::log(
                action: 'permanently_deleted_job',
                description: "{$adminName} permanently deleted Job Card {$lockedJobCard->job_number}. Reason: {$reason}.".($explanation ? " Explanation: {$explanation}." : '').($totalCreditPreserved > 0 ? ' LKR '.number_format($totalCreditPreserved, 2).' preserved as unapplied customer credit.' : '').' [Audit Data: '.json_encode($auditPayload).']',
                module: 'Job Cards',
                userId: $user->id
            );

            // Clear circular foreign key on job_cards before deleting or voiding invoices
            $lockedJobCard->update(['invoice_id' => null]);

            foreach ($linkedInvoices as $inv) {
                $hasPaymentHistory = Payment::where('invoice_id', $inv->id)->exists()
                    || PaymentAllocation::where('invoice_id', $inv->id)->exists();

                if (! $hasPaymentHistory && $inv->status === 'draft') {
                    $inv->items()->delete();
                    $inv->delete();
                } else {
                    // Retain legally mandatory financial history: void and remove from receivables
                    $inv->update([
                        'status' => 'cancelled',
                        'amount_paid' => 0.00,
                        'balance_amount' => 0.00,
                        'job_card_id' => null,
                        'cancellation_reason' => "Job Card #{$jobNumber} permanently deleted by {$adminName}. Reason: {$reason}",
                        'cancelled_by' => $user->id,
                        'cancelled_at' => now(),
                        'notes' => trim(($inv->notes ?? '')."\nVoided: Job Card #{$jobNumber} permanently deleted on {$deletionTimestamp}."),
                    ]);
                }
            }

            // Unlink any appointments
            Appointment::where('job_card_id', $lockedJobCard->id)->update(['job_card_id' => null]);

            // Delete dependent records
            $lockedJobCard->services()->delete();
            $lockedJobCard->products()->delete();
            $lockedJobCard->issues()->delete();
            $lockedJobCard->carpets()->delete();

            // Delete original Job Card
            $lockedJobCard->delete();

            DB::commit();

            $msg = 'Job permanently deleted successfully.';
            if ($totalCreditPreserved > 0) {
                $msg .= ' LKR '.number_format($totalCreditPreserved, 2).' was moved into unapplied customer credit.';
            }

            return redirect()->route('operations.job-cards.index')
                ->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Failed to permanently delete Job Card: {$e->getMessage()}", [
                'job_card_id' => $jobCard->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Error permanently deleting job: '.$e->getMessage());
        }
    }
}
