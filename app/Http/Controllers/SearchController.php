<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Service;
use App\Models\Supplier;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Search Employees by Name, Employee Number, or Mobile.
     */
    public function employees(Request $request): JsonResponse
    {
        $id = $request->query('id');
        if (! empty($id)) {
            $emp = Employee::find($id);
            if (! $emp) {
                return response()->json([]);
            }

            return response()->json([[
                'id' => $emp->id,
                'label' => $emp->name.' ('.($emp->employee_number ?? 'EMP-'.$emp->id).')',
                'subtext' => ($emp->position ? $emp->position.' • ' : '').($emp->mobile ?? 'No phone'),
                'name' => $emp->name,
                'employee_number' => $emp->employee_number,
                'mobile' => $emp->mobile,
                'daily_wage' => (float) $emp->daily_wage,
                'monthly_basic_salary' => (float) $emp->monthly_basic_salary,
            ]]);
        }

        $term = trim((string) $request->query('q', ''));
        $branchId = $request->query('branch_id');

        $query = Employee::query()->where('is_active', true);

        if (! empty($branchId)) {
            $query->where('branch_id', $branchId);
        }

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('employee_number', 'like', "%{$term}%")
                    ->orWhere('mobile', 'like', "%{$term}%");
            });
        }

        $employees = $query->orderBy('name')->limit(20)->get();

        return response()->json($employees->map(function ($emp) {
            return [
                'id' => $emp->id,
                'label' => $emp->name.' ('.($emp->employee_number ?? 'EMP-'.$emp->id).')',
                'subtext' => ($emp->position ? $emp->position.' • ' : '').($emp->mobile ?? 'No phone'),
                'name' => $emp->name,
                'employee_number' => $emp->employee_number,
                'mobile' => $emp->mobile,
                'daily_wage' => (float) $emp->daily_wage,
                'monthly_basic_salary' => (float) $emp->monthly_basic_salary,
            ];
        }));
    }

    /**
     * Search Customers by Name, Phone, Company, or Registration Number.
     */
    public function customers(Request $request): JsonResponse
    {
        $id = $request->query('id');
        if (! empty($id)) {
            $c = Customer::find($id);
            if (! $c) {
                return response()->json([]);
            }

            return response()->json([[
                'id' => $c->id,
                'label' => $c->customer_name.($c->company_name ? ' — ['.$c->company_name.']' : ''),
                'subtext' => ($c->phone ? $c->phone.' • ' : '').($c->address ?? ''),
                'name' => $c->customer_name,
                'phone' => $c->phone,
                'email' => $c->email,
            ]]);
        }

        $term = trim((string) $request->query('q', ''));

        $query = Customer::query()->where('status', 'active');

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('customer_name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('company_name', 'like', "%{$term}%")
                    ->orWhere('registration_number', 'like', "%{$term}%");
            });
        }

        $customers = $query->orderBy('customer_name')->limit(20)->get();

        return response()->json($customers->map(function ($c) {
            return [
                'id' => $c->id,
                'label' => $c->customer_name.($c->company_name ? ' — ['.$c->company_name.']' : ''),
                'subtext' => ($c->phone ? $c->phone.' • ' : '').($c->address ?? ''),
                'name' => $c->customer_name,
                'phone' => $c->phone,
                'email' => $c->email,
            ];
        }));
    }

    /**
     * Search Vehicles by Registration Number, Make/Model, or Customer.
     */
    public function vehicles(Request $request): JsonResponse
    {
        $id = $request->query('id');
        if (! empty($id)) {
            $v = Vehicle::with('customer')->find($id);
            if (! $v) {
                return response()->json([]);
            }

            return response()->json([[
                'id' => $v->id,
                'label' => $v->vehicle_number.($v->make_model ? ' ('.$v->make_model.')' : ($v->brand ? ' ('.$v->brand.' '.$v->model.')' : '')),
                'subtext' => 'Owner: '.($v->customer?->customer_name ?? 'N/A').($v->customer?->phone ? ' • '.$v->customer->phone : ''),
                'customer_id' => $v->customer_id,
                'vehicle_number' => $v->vehicle_number,
            ]]);
        }

        $term = trim((string) $request->query('q', ''));
        $customerId = $request->query('customer_id');

        $query = Vehicle::with('customer');

        if (! empty($customerId)) {
            $query->where('customer_id', $customerId);
        }

        if ($term !== '') {
            $normalized = Vehicle::normalizeNumber($term);
            $query->where(function ($q) use ($term, $normalized) {
                $q->where('vehicle_number', 'like', "%{$term}%")
                    ->orWhere('vehicle_number_normalized', 'like', "%{$normalized}%")
                    ->orWhere('make_model', 'like', "%{$term}%")
                    ->orWhere('brand', 'like', "%{$term}%")
                    ->orWhere('model', 'like', "%{$term}%")
                    ->orWhereHas('customer', function ($cq) use ($term) {
                        $cq->where('customer_name', 'like', "%{$term}%")
                            ->orWhere('phone', 'like', "%{$term}%");
                    });
            });
        }

        $vehicles = $query->orderBy('vehicle_number')->limit(20)->get();

        return response()->json($vehicles->map(function ($v) {
            return [
                'id' => $v->id,
                'label' => $v->vehicle_number.($v->make_model ? ' ('.$v->make_model.')' : ($v->brand ? ' ('.$v->brand.' '.$v->model.')' : '')),
                'subtext' => 'Owner: '.($v->customer?->customer_name ?? 'N/A').($v->customer?->phone ? ' • '.$v->customer->phone : ''),
                'customer_id' => $v->customer_id,
                'vehicle_number' => $v->vehicle_number,
            ];
        }));
    }

    /**
     * Search Suppliers by Name, Registration Number, or Phone.
     */
    public function suppliers(Request $request): JsonResponse
    {
        $id = $request->query('id');
        if (! empty($id)) {
            $s = Supplier::find($id);
            if (! $s) {
                return response()->json([]);
            }

            return response()->json([[
                'id' => $s->id,
                'label' => $s->name,
                'subtext' => ($s->registration_number ? 'Reg: '.$s->registration_number.' • ' : '').($s->mobile ?: $s->office_phone ?: 'No phone'),
                'name' => $s->name,
                'mobile' => $s->mobile,
            ]]);
        }

        $term = trim((string) $request->query('q', ''));

        $query = Supplier::active();

        if ($term !== '') {
            $query->search($term);
        }

        $suppliers = $query->orderBy('name')->limit(20)->get();

        return response()->json($suppliers->map(function ($s) {
            return [
                'id' => $s->id,
                'label' => $s->name,
                'subtext' => ($s->registration_number ? 'Reg: '.$s->registration_number.' • ' : '').($s->mobile ?: $s->office_phone ?: 'No phone'),
                'name' => $s->name,
                'mobile' => $s->mobile,
            ];
        }));
    }

    /**
     * Search Products by Name, SKU, or Barcode.
     */
    public function products(Request $request): JsonResponse
    {
        $id = $request->query('id');
        if (! empty($id)) {
            $p = Product::with('uom')->find($id);
            if (! $p) {
                return response()->json([]);
            }

            return response()->json([[
                'id' => $p->id,
                'label' => $p->name.($p->sku ? ' ('.$p->sku.')' : ''),
                'subtext' => 'Price: LKR '.number_format((float) $p->selling_price, 2).' • '.($p->uom?->name ?? 'Unit'),
                'selling_price' => (float) $p->selling_price,
                'name' => $p->name,
                'sku' => $p->sku,
            ]]);
        }

        $term = trim((string) $request->query('q', ''));

        $query = Product::active()->with('uom');

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('sku', 'like', "%{$term}%")
                    ->orWhere('barcode', 'like', "%{$term}%");
            });
        }

        $products = $query->orderBy('name')->limit(20)->get();

        return response()->json($products->map(function ($p) {
            return [
                'id' => $p->id,
                'label' => $p->name.($p->sku ? ' ('.$p->sku.')' : ''),
                'subtext' => 'Price: LKR '.number_format((float) $p->selling_price, 2).' • '.($p->uom?->name ?? 'Unit'),
                'selling_price' => (float) $p->selling_price,
                'name' => $p->name,
                'sku' => $p->sku,
            ];
        }));
    }

    /**
     * Search Services by Name or Code.
     */
    public function services(Request $request): JsonResponse
    {
        $id = $request->query('id');
        if (! empty($id)) {
            $s = Service::find($id);
            if (! $s) {
                return response()->json([]);
            }

            return response()->json([[
                'id' => $s->id,
                'label' => $s->name.($s->code ? ' ('.$s->code.')' : ''),
                'subtext' => $s->description ?? '',
                'name' => $s->name,
                'code' => $s->code,
            ]]);
        }

        $term = trim((string) $request->query('q', ''));

        $query = Service::active();

        if ($term !== '') {
            $query->search($term);
        }

        $services = $query->orderBy('name')->limit(20)->get();

        return response()->json($services->map(function ($s) {
            return [
                'id' => $s->id,
                'label' => $s->name.($s->code ? ' ('.$s->code.')' : ''),
                'subtext' => $s->description ?? '',
                'name' => $s->name,
                'code' => $s->code,
            ];
        }));
    }

    /**
     * Search Invoices by Invoice Number, Customer, or Vehicle Number.
     */
    public function invoices(Request $request): JsonResponse
    {
        $id = $request->query('id');
        if (! empty($id)) {
            $inv = Invoice::with(['customer', 'jobCard.vehicle'])->find($id);
            if (! $inv) {
                return response()->json([]);
            }

            return response()->json([[
                'id' => $inv->id,
                'label' => $inv->invoice_number,
                'subtext' => ($inv->customer?->customer_name ?? 'Client').' • Balance: LKR '.number_format((float) $inv->balance_amount, 2),
                'invoice_number' => $inv->invoice_number,
                'grand_total' => (float) $inv->grand_total,
                'balance_amount' => (float) $inv->balance_amount,
                'customer_id' => $inv->customer_id,
            ]]);
        }

        $term = trim((string) $request->query('q', ''));
        $customerId = $request->query('customer_id');
        $unpaidOnly = $request->boolean('unpaid_only');

        $query = Invoice::with(['customer', 'jobCard.vehicle'])
            ->where('status', '!=', 'cancelled');

        if ($unpaidOnly) {
            $query->where('balance_amount', '>', 0);
        }

        if (! empty($customerId)) {
            $query->where('customer_id', $customerId);
        }

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('invoice_number', 'like', "%{$term}%")
                    ->orWhereHas('customer', function ($cq) use ($term) {
                        $cq->where('customer_name', 'like', "%{$term}%")
                            ->orWhere('phone', 'like', "%{$term}%");
                    })
                    ->orWhereHas('jobCard.vehicle', function ($vq) use ($term) {
                        $vq->where('vehicle_number', 'like', "%{$term}%");
                    });
            });
        }

        $invoices = $query->orderByDesc('id')->limit(20)->get();

        return response()->json($invoices->map(function ($inv) {
            return [
                'id' => $inv->id,
                'label' => $inv->invoice_number,
                'subtext' => ($inv->customer?->customer_name ?? 'Client').' • Balance: LKR '.number_format((float) $inv->balance_amount, 2),
                'invoice_number' => $inv->invoice_number,
                'grand_total' => (float) $inv->grand_total,
                'balance_amount' => (float) $inv->balance_amount,
                'customer_id' => $inv->customer_id,
            ];
        }));
    }
}
