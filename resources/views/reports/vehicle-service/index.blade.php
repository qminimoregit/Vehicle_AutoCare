@extends('layouts.app')

@section('title', 'Vehicle Service Report')

@section('content')
<div class="space-y-6" x-data="vehicleServiceReportApp()">
    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#0B1849] tracking-tight">Vehicle Service Report</h1>
            <p class="text-sm text-gray-500 mt-1">Complete service log and maintenance history for licensing and customer records.</p>
        </div>
        @if($selectedVehicle)
            <div class="flex items-center gap-2">
                <a 
                    href="{{ route('reports.vehicle-service.print', array_merge(['vehicle' => $selectedVehicle->id], request()->query())) }}" 
                    target="_blank" 
                    class="inline-flex items-center px-3.5 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition shadow-xs"
                >
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Service History
                </a>
                <a 
                    href="{{ route('reports.vehicle-service.export', array_merge(['vehicle' => $selectedVehicle->id], request()->query())) }}" 
                    class="inline-flex items-center px-3.5 py-2 rounded-lg bg-[#0B1849] text-xs font-semibold text-white hover:bg-[#0B1849]/90 transition shadow-xs"
                >
                    <svg class="w-4 h-4 mr-1.5 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Excel
                </a>
            </div>
        @endif
    </div>

    <!-- Search Box Card with Live Autocomplete -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs relative">
        <form method="GET" action="{{ route('reports.vehicle-service.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-end">
            <!-- Search Input with Auto Suggest -->
            <div class="relative">
                <label for="search" class="block text-xs font-semibold text-gray-600 mb-1">Search Vehicle # / Customer / Phone</label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="search" 
                        name="search" 
                        x-model="searchTerm" 
                        @input.debounce.300ms="fetchSuggestions()" 
                        placeholder="e.g. CAB-1234, John, 077..." 
                        class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849] pl-8"
                    >
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <!-- Suggestions Dropdown -->
                <div 
                    x-show="suggestions.length > 0" 
                    x-cloak 
                    @click.away="suggestions = []" 
                    class="absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-30 max-h-56 overflow-y-auto divide-y divide-gray-100"
                >
                    <template x-for="item in suggestions" :key="item.id">
                        <div 
                            @click="selectVehicle(item)" 
                            class="p-2.5 hover:bg-slate-50 cursor-pointer text-xs"
                        >
                            <div class="font-bold text-[#0B1849]" x-text="item.vehicle_number"></div>
                            <div class="text-[11px] text-gray-500" x-text="item.make_model + ' • ' + item.customer_name + ' (' + item.phone + ')'"></div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Date From -->
            <div>
                <label for="date_from" class="block text-xs font-semibold text-gray-600 mb-1">Date From (Optional)</label>
                <input 
                    type="date" 
                    id="date_from" 
                    name="date_from" 
                    value="{{ $dateFrom }}" 
                    class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]"
                >
            </div>

            <!-- Date To -->
            <div>
                <label for="date_to" class="block text-xs font-semibold text-gray-600 mb-1">Date To (Optional)</label>
                <input 
                    type="date" 
                    id="date_to" 
                    name="date_to" 
                    value="{{ $dateTo }}" 
                    class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]"
                >
            </div>

            <!-- Search Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-[#0B1849] text-white text-xs font-semibold rounded-lg hover:bg-[#0B1849]/90 transition">
                    View History
                </button>
                <a href="{{ route('reports.vehicle-service.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                    Clear
                </a>
            </div>
        </form>
    </div>

    @if($selectedVehicle)
        <!-- Vehicle & Customer Profile Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-5 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Vehicle Info -->
            <div class="space-y-1.5 border-b md:border-b-0 md:border-r border-gray-100 pb-4 md:pb-0 md:pr-4">
                <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Vehicle Information</div>
                <div class="text-xl font-bold text-[#0B1849] font-mono">{{ $selectedVehicle->vehicle_number }}</div>
                <div class="text-xs font-medium text-gray-700">{{ $selectedVehicle->make_model ?? 'Vehicle' }} {{ $selectedVehicle->year_of_manufacture ? '('.$selectedVehicle->year_of_manufacture.')' : '' }}</div>
                <div class="text-xs text-gray-500">Type: <span class="font-medium text-gray-700">{{ $selectedVehicle->vehicleType?->name ?? 'Standard' }}</span></div>
                @if($selectedVehicle->chassis_number)
                    <div class="text-[11px] font-mono text-gray-400">Chassis: {{ $selectedVehicle->chassis_number }}</div>
                @endif
            </div>

            <!-- Customer Info -->
            <div class="space-y-1.5 border-b md:border-b-0 md:border-r border-gray-100 pb-4 md:pb-0 md:pr-4">
                <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Registered Owner</div>
                <div class="text-base font-bold text-gray-900">{{ $selectedVehicle->customer?->display_name ?? 'Walk-in Customer' }}</div>
                <div class="text-xs text-gray-600">Phone: <span class="font-medium">{{ $selectedVehicle->customer?->phone ?? 'N/A' }}</span></div>
                <div class="text-xs text-gray-500">Account: <span class="font-medium text-gray-700 capitalize">{{ $selectedVehicle->customer?->customer_type ?? 'Individual' }}</span></div>
            </div>

            <!-- Mileage & Timeline Metrics -->
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                    <span class="text-[11px] text-gray-500 block">Total Services</span>
                    <span class="text-base font-bold text-[#0B1849]">{{ $summary['total_services'] }}</span>
                </div>
                <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                    <span class="text-[11px] text-gray-500 block">Latest Service</span>
                    <span class="text-xs font-bold text-gray-800">{{ $summary['latest_service_date'] ?? 'N/A' }}</span>
                </div>
                <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                    <span class="text-[11px] text-gray-500 block">Latest Mileage</span>
                    <span class="text-xs font-bold text-gray-800">{{ $summary['latest_mileage'] ? number_format($summary['latest_mileage']).' km' : 'N/A' }}</span>
                </div>
                <div class="bg-emerald-50 p-2.5 rounded-lg border border-emerald-100">
                    <span class="text-[11px] text-emerald-700 block font-medium">Next Due Mileage</span>
                    <span class="text-xs font-bold text-emerald-800">{{ $summary['next_service_mileage'] ? number_format($summary['next_service_mileage']).' km' : 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Service History Table -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-[#0B1849]">Maintenance & Service Log</span>
                <span class="text-xs text-gray-500">{{ $serviceHistory->count() }} records found</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase tracking-wider">
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Job Number</th>
                            <th class="py-3 px-4 text-center">Mileage</th>
                            <th class="py-3 px-4">Services Performed</th>
                            <th class="py-3 px-4">Products & Parts Used</th>
                            <th class="py-3 px-4">Invoice #</th>
                            <th class="py-3 px-4 text-right">Amount</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($serviceHistory as $job)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-4 text-gray-600 whitespace-nowrap">
                                    {{ $job->date?->format('d M Y') ?? '-' }}
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-[#0B1849]">
                                    <a href="{{ route('operations.job-cards.show', $job) }}" class="hover:underline">
                                        {{ $job->job_number }}
                                    </a>
                                </td>
                                <td class="py-3 px-4 text-center font-mono font-semibold text-gray-800">
                                    {{ $job->current_mileage ? number_format($job->current_mileage).' km' : '-' }}
                                </td>
                                <td class="py-3 px-4 max-w-[200px]">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($job->services as $js)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] bg-blue-50 text-blue-800 border border-blue-100">
                                                {{ $js->service_name ?? $js->service?->name ?? 'Service' }}
                                            </span>
                                        @empty
                                            <span class="text-gray-400 italic">No services listed</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="py-3 px-4 max-w-[220px]">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($job->products as $jp)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] bg-gray-100 text-gray-700">
                                                {{ $jp->product_name ?? 'Item' }} (×@quantity($jp->quantity))
                                            </span>
                                        @empty
                                            <span class="text-gray-400 italic">No parts charged</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-mono">
                                    @if($job->invoice)
                                        <a href="{{ route('billing.invoices.show', $job->invoice) }}" class="text-blue-600 hover:underline">
                                            {{ $job->invoice->invoice_number }}
                                        </a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-gray-900">
                                    LKR {{ number_format($job->grand_total, 2) }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                                        {{ ucfirst($job->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-gray-500 text-xs">
                                    No previous service history found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Empty State Prompt -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <h3 class="text-base font-bold text-[#0B1849]">Select a Registered Vehicle</h3>
            <p class="text-xs text-gray-500 max-w-md mx-auto mt-1">Enter a vehicle registration plate number, customer name, or mobile number above to generate their complete service history.</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
function vehicleServiceReportApp() {
    return {
        searchTerm: '{{ $search }}',
        suggestions: [],

        fetchSuggestions() {
            if (this.searchTerm.length < 2) {
                this.suggestions = [];
                return;
            }

            fetch(`/reports/vehicle-service/search?term=${encodeURIComponent(this.searchTerm)}`)
                .then(res => res.json())
                .then(data => {
                    this.suggestions = data;
                })
                .catch(() => {
                    this.suggestions = [];
                });
        },

        selectVehicle(item) {
            this.searchTerm = item.vehicle_number;
            this.suggestions = [];
            window.location.href = `{{ route('reports.vehicle-service.index') }}?vehicle_id=${item.id}&search=${encodeURIComponent(item.vehicle_number)}`;
        }
    };
}
</script>
@endpush
@endsection
