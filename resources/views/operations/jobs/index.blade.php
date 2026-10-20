@extends('layouts.app')

@section('title', 'Job Cards')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <x-page-header 
        title="Job Cards" 
        description="Monitor vehicle services, progress, technician assignments, and billing."
    >
        <x-slot:actions>
            <a 
                href="{{ route('operations.vehicles.index') }}" 
                class="inline-flex items-center justify-center px-4 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg shadow-xs transition duration-150 cursor-pointer min-h-[44px]"
            >
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Vehicle Job
            </a>
        </x-slot:actions>
    </x-page-header>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('warning'))
        <x-alert type="warning" :message="session('warning')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <!-- Quick Status Tabs -->
    <div class="flex items-center overflow-x-auto space-x-2 pb-1 border-b border-gray-200 text-xs sm:text-sm font-semibold scrollbar-none">
        <a 
            href="{{ route('operations.job-cards.index') }}" 
            class="px-3.5 py-2 rounded-lg transition whitespace-nowrap {{ $status === 'all' && empty($dateFilter) ? 'bg-[#0B1849] text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}"
        >
            All Jobs ({{ $counts['all'] }})
        </a>
        <a 
            href="{{ route('operations.job-cards.index', ['filter' => 'today']) }}" 
            class="px-3.5 py-2 rounded-lg transition whitespace-nowrap {{ $dateFilter === 'today' ? 'bg-[#0B1849] text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}"
        >
            Today's Jobs ({{ $counts['today'] }})
        </a>
        <a 
            href="{{ route('operations.job-cards.index', ['status' => 'pending']) }}" 
            class="px-3.5 py-2 rounded-lg transition whitespace-nowrap {{ $status === 'pending' && empty($dateFilter) ? 'bg-amber-500 text-white shadow-xs' : 'text-amber-700 hover:bg-amber-50' }}"
        >
            Pending ({{ $counts['pending'] }})
        </a>
        <a 
            href="{{ route('operations.job-cards.index', ['status' => 'in_progress']) }}" 
            class="px-3.5 py-2 rounded-lg transition whitespace-nowrap {{ $status === 'in_progress' ? 'bg-blue-600 text-white shadow-xs' : 'text-blue-700 hover:bg-blue-50' }}"
        >
            In Progress ({{ $counts['in_progress'] }})
        </a>
        <a 
            href="{{ route('operations.job-cards.index', ['status' => 'completed']) }}" 
            class="px-3.5 py-2 rounded-lg transition whitespace-nowrap {{ $status === 'completed' ? 'bg-emerald-600 text-white shadow-xs' : 'text-emerald-700 hover:bg-emerald-50' }}"
        >
            Completed ({{ $counts['completed'] }})
        </a>
        <a 
            href="{{ route('operations.job-cards.index', ['filter' => 'previous_pending']) }}" 
            class="px-3.5 py-2 rounded-lg transition whitespace-nowrap {{ $dateFilter === 'previous_pending' ? 'bg-red-600 text-white shadow-xs' : 'text-red-700 hover:bg-red-50' }}"
        >
            Previous Pending ({{ $counts['previous_pending'] }})
        </a>
    </div>

    <!-- Search Bar -->
    <x-search-bar 
        :action="route('operations.job-cards.index')" 
        :search="$search" 
        placeholder="Search by Job #, Vehicle Number, Customer, Phone..."
        :resetUrl="route('operations.job-cards.index')"
    />

    <!-- Desktop Data Table View -->
    <div class="hidden md:block bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">Job Number</th>
                        <th class="px-4 py-3.5">Date</th>
                        <th class="px-4 py-3.5">Vehicle</th>
                        <th class="px-4 py-3.5">Customer</th>
                        <th class="px-4 py-3.5">Mileage</th>
                        <th class="px-4 py-3.5">Assigned To</th>
                        <th class="px-4 py-3.5">Grand Total</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-800">
                    @forelse($jobs as $job)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3.5 font-bold text-[#0B1849]">
                                <a href="{{ route('operations.job-cards.show', $job) }}" class="hover:underline">
                                    {{ $job->job_number }}
                                </a>
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-500 whitespace-nowrap">
                                {{ $job->date ? $job->date->format('Y-m-d') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-gray-900 tracking-wide">{{ $job->vehicle->vehicle_number }}</div>
                                <div class="text-xs text-gray-500">{{ $job->vehicleType->name }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-gray-900">{{ $job->customer->customer_name }}</div>
                                <div class="text-xs text-gray-500">{{ $job->customer->phone }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-xs font-semibold text-gray-700 whitespace-nowrap">
                                {{ $job->current_mileage > 0 ? number_format($job->current_mileage) . ' km' : 'N/A' }}
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-600">
                                {{ $job->assignedTechnician?->name ?? 'Unassigned' }}
                            </td>
                            <td class="px-4 py-3.5 font-bold text-gray-900 whitespace-nowrap">
                                LKR {{ number_format($job->grand_total, 2) }}
                            </td>
                            <td class="px-4 py-3.5">
                                @if($job->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Pending</span>
                                @elseif($job->status === 'in_progress')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">In Progress</span>
                                @elseif($job->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Completed</span>
                                @elseif($job->status === 'delivered')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">Delivered</span>
                                @elseif($job->status === 'cancelled')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">Cancelled</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <a 
                                    href="{{ route('operations.job-cards.show', $job) }}" 
                                    class="inline-flex items-center px-3 py-1.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-semibold rounded-md shadow-xs transition"
                                >
                                    View Job
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-10 w-10 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="text-sm font-semibold text-gray-700">No Job Cards found.</p>
                                <p class="text-xs text-gray-400 mt-0.5">Register a vehicle above to initiate the first job card.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile / Tablet Responsive Card View (No Horizontal Overflow) -->
    <div class="block md:hidden space-y-3">
        @forelse($jobs as $job)
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <a href="{{ route('operations.job-cards.show', $job) }}" class="text-base font-bold text-[#0B1849]">
                            {{ $job->job_number }}
                        </a>
                        <div class="text-xs text-gray-500 mt-0.5">
                            {{ $job->date ? $job->date->format('M d, Y') : 'N/A' }}
                        </div>
                    </div>

                    <div>
                        @if($job->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Pending</span>
                        @elseif($job->status === 'in_progress')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">In Progress</span>
                        @elseif($job->status === 'completed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Completed</span>
                        @elseif($job->status === 'delivered')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">Delivered</span>
                        @elseif($job->status === 'cancelled')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">Cancelled</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs bg-slate-50 p-2.5 rounded-lg border border-gray-100">
                    <div>
                        <span class="text-gray-400 uppercase text-[10px] font-bold block">Vehicle</span>
                        <span class="font-bold text-gray-900">{{ $job->vehicle->vehicle_number }}</span>
                        <span class="text-gray-500 block text-[11px]">{{ $job->vehicleType->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 uppercase text-[10px] font-bold block">Customer</span>
                        <span class="font-semibold text-gray-900 block truncate">{{ $job->customer->customer_name }}</span>
                        <span class="text-gray-500 block text-[11px]">{{ $job->customer->phone }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 uppercase text-[10px] font-bold block">Mileage</span>
                        <span class="font-semibold text-gray-800">{{ $job->current_mileage > 0 ? number_format($job->current_mileage) . ' km' : 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 uppercase text-[10px] font-bold block">Grand Total</span>
                        <span class="font-bold text-gray-900">LKR {{ number_format($job->grand_total, 2) }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <span class="text-xs text-gray-500">
                        Assigned: <strong class="text-gray-700">{{ $job->assignedTechnician?->name ?? 'Unassigned' }}</strong>
                    </span>
                    <a 
                        href="{{ route('operations.job-cards.show', $job) }}" 
                        class="inline-flex items-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-bold rounded-lg shadow-xs transition min-h-[44px]"
                    >
                        View Job Card
                        <svg class="w-3.5 h-3.5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-xl p-8 text-center text-gray-500">
                <p class="text-sm font-semibold text-gray-700">No Job Cards found.</p>
                <p class="text-xs text-gray-400 mt-1">Register a vehicle above to start a job.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $jobs->links() }}
    </div>
</div>
@endsection
