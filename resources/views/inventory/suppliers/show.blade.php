@extends('layouts.app')

@section('title', 'Supplier Details - ' . $supplier->name)

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('inventory.suppliers.index') }}" class="text-xs font-semibold text-[#0B1849] hover:underline inline-flex items-center mb-2">
                    &larr; Back to Suppliers
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ $supplier->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">Supplier profile, contact info, and activity history.</p>
            </div>
            @if(Auth::user()->isAdmin())
                <div class="flex items-center space-x-3">
                    <a 
                        href="{{ route('inventory.suppliers.edit', $supplier) }}" 
                        class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg text-[#0B1849] bg-blue-50 hover:bg-blue-100 border border-blue-200 transition"
                    >
                        Edit Supplier
                    </a>
                </div>
            @endif
        </div>

        <!-- Supplier Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Basic Info -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Supplier Overview</h3>
                <div>
                    <span class="text-xs text-gray-500 block">Registration Number</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $supplier->registration_number ?: 'Not specified' }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Account Status</span>
                    @if($supplier->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 mt-0.5">
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-300 mt-0.5">
                            Inactive
                        </span>
                    @endif
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Member Since</span>
                    <span class="text-sm text-gray-700">{{ $supplier->created_at->format('M d, Y') }}</span>
                </div>
            </div>

            <!-- Contact Details -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Communication</h3>
                <div>
                    <span class="text-xs text-gray-500 block">Mobile Phone</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $supplier->mobile ?: 'Not provided' }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Office Landline</span>
                    <span class="text-sm text-gray-700">{{ $supplier->office_phone ?: 'Not provided' }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Fax</span>
                    <span class="text-sm text-gray-700">{{ $supplier->fax ?: 'Not provided' }}</span>
                </div>
            </div>

            <!-- Location -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Address & Facility</h3>
                <div>
                    <span class="text-xs text-gray-500 block">Billing / Dispatch Address</span>
                    <p class="text-sm text-gray-800 whitespace-pre-line mt-1">{{ $supplier->address ?: 'No physical address on file.' }}</p>
                </div>
            </div>
        </div>

        <!-- Recent Purchases (GRNs) -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Recent Goods Received Notes (GRN)</h3>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('inventory.grn.create', ['supplier_id' => $supplier->id]) }}" class="text-xs font-semibold text-[#0B1849] hover:underline">
                        + New GRN for this Supplier
                    </a>
                @endif
            </div>

            <div class="responsive-table-wrapper">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">GRN No</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Invoice No</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Date</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase">Total (LKR)</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($supplier->goodsReceivedNotes as $grn)
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="px-6 py-3 text-sm font-semibold text-gray-900">
                                    <a href="{{ route('inventory.grn.show', $grn) }}" class="text-[#0B1849] hover:underline">
                                        {{ $grn->grn_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $grn->supplier_invoice_number ?: '-' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $grn->received_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-3 text-sm text-gray-900 text-right font-mono">{{ number_format($grn->total_amount, 2) }}</td>
                                <td class="px-6 py-3 text-center">
                                    @if($grn->status === 'confirmed')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Confirmed</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Draft</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-6 text-center text-xs text-gray-400">
                                    No purchase orders or GRNs recorded yet with this vendor.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
