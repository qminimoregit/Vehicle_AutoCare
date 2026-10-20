@extends('layouts.app')

@section('title', 'Operations Dashboard')

@section('content')
    <!-- Dashboard Header -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">AutoCare Center Dashboard</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Daily workshop operations overview & center master records. Today is <span class="font-medium text-gray-700">{{ now()->format('l, d F Y') }}</span>.
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                    Workshop Active
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Actions Banner (Clear, Simple, Marked Coming Soon) -->
    <div class="mb-8 bg-white border border-gray-200 rounded-xl p-5 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-gray-700">Quick Workshop Actions</h2>
                <p class="text-xs text-gray-500">Shortcut buttons for day-to-day shop floor workflows.</p>
            </div>
            <span class="text-xs bg-amber-50 text-amber-700 px-2.5 py-0.5 rounded-full font-semibold border border-amber-200">
                Phase 2 Modules Coming Soon
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <!-- New Job (Disabled / Coming Soon) -->
            <button 
                type="button" 
                disabled 
                class="flex flex-col items-center justify-center p-3.5 rounded-lg border border-gray-200 bg-gray-50/70 text-gray-400 cursor-not-allowed transition group relative"
                title="Jobs module will be enabled in Phase 2"
            >
                <div class="p-2 rounded-lg bg-gray-100 text-gray-400 mb-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-gray-600">New Job</span>
                <span class="text-[10px] text-amber-600 font-medium mt-0.5">Coming Soon</span>
            </button>

            <!-- New Customer (Disabled / Coming Soon) -->
            <button 
                type="button" 
                disabled 
                class="flex flex-col items-center justify-center p-3.5 rounded-lg border border-gray-200 bg-gray-50/70 text-gray-400 cursor-not-allowed transition group relative"
                title="Customer & Vehicle module will be enabled in Phase 2"
            >
                <div class="p-2 rounded-lg bg-gray-100 text-gray-400 mb-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-gray-600">New Customer</span>
                <span class="text-[10px] text-amber-600 font-medium mt-0.5">Coming Soon</span>
            </button>

            <!-- New Quotation (Disabled / Coming Soon) -->
            <button 
                type="button" 
                disabled 
                class="flex flex-col items-center justify-center p-3.5 rounded-lg border border-gray-200 bg-gray-50/70 text-gray-400 cursor-not-allowed transition group relative"
                title="Quotation integration ready for integration in Phase 2"
            >
                <div class="p-2 rounded-lg bg-gray-100 text-gray-400 mb-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-gray-600">New Quotation</span>
                <span class="text-[10px] text-amber-600 font-medium mt-0.5">Coming Soon</span>
            </button>

            <!-- New Invoice (Disabled / Coming Soon) -->
            <button 
                type="button" 
                disabled 
                class="flex flex-col items-center justify-center p-3.5 rounded-lg border border-gray-200 bg-gray-50/70 text-gray-400 cursor-not-allowed transition group relative"
                title="Invoice integration ready for integration in Phase 2"
            >
                <div class="p-2 rounded-lg bg-gray-100 text-gray-400 mb-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-gray-600">New Invoice</span>
                <span class="text-[10px] text-amber-600 font-medium mt-0.5">Coming Soon</span>
            </button>
        </div>
    </div>

    <!-- 5 Real Workshop Operation Metrics -->
    <div class="mb-8">
        <h2 class="text-sm font-bold uppercase tracking-wider text-gray-700 mb-3">Workshop Performance Metrics</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Today's Jobs -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-gray-500 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider">Today's Jobs</span>
                    <div class="p-2 rounded-lg bg-blue-50 text-blue-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $todayJobs }}</div>
                    <div class="text-[11px] text-gray-400 mt-1">Logged vehicles today</div>
                </div>
            </div>

            <!-- Pending Jobs -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-gray-500 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider">Pending Jobs</span>
                    <div class="p-2 rounded-lg bg-amber-50 text-amber-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-amber-600">{{ $pendingJobs }}</div>
                    <div class="text-[11px] text-gray-400 mt-1">In progress on floor</div>
                </div>
            </div>

            <!-- Completed Jobs -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-gray-500 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider">Completed Jobs</span>
                    <div class="p-2 rounded-lg bg-emerald-50 text-emerald-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-emerald-600">{{ $completedJobs }}</div>
                    <div class="text-[11px] text-gray-400 mt-1">Ready for collection</div>
                </div>
            </div>

            <!-- Today's Sales -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-gray-500 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider">Today's Sales</span>
                    <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">${{ number_format($todaySales, 2) }}</div>
                    <div class="text-[11px] text-gray-400 mt-1">Total revenue cleared</div>
                </div>
            </div>

            <!-- Pending Payments -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-gray-500 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider">Pending Payments</span>
                    <div class="p-2 rounded-lg bg-rose-50 text-rose-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-rose-600">${{ number_format($pendingPayments, 2) }}</div>
                    <div class="text-[11px] text-gray-400 mt-1">Outstanding receivables</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Master Data Setup Status & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Master Data Status Cards -->
        <div class="lg:col-span-2 space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-gray-700">Master Data Configuration (Phase 1)</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Services Card -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs hover:border-blue-300 transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Service Catalog</span>
                            <h3 class="text-base font-bold text-gray-900 mt-1">Service Lists</h3>
                            <p class="text-xs text-gray-500 mt-1">Manage workshop services, service codes, descriptions, and activation status.</p>
                        </div>
                        <div class="p-2.5 rounded-lg bg-blue-50 text-blue-600">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                        <div class="text-xs text-gray-600">
                            <span class="font-bold text-gray-900 text-sm">{{ $totalServices }}</span> Total (<span class="text-emerald-600 font-semibold">{{ $activeServices }} Active</span>)
                        </div>
                        <a href="{{ route('master-data.services.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center">
                            Manage Services &rarr;
                        </a>
                    </div>
                </div>

                <!-- Vehicle Types Card -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs hover:border-blue-300 transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Classification</span>
                            <h3 class="text-base font-bold text-gray-900 mt-1">Vehicle Types</h3>
                            <p class="text-xs text-gray-500 mt-1">Car, Van, SUV, Motorbike, Lorry, and custom workshop categories.</p>
                        </div>
                        <div class="p-2.5 rounded-lg bg-blue-50 text-blue-600">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                        <div class="text-xs text-gray-600">
                            <span class="font-bold text-gray-900 text-sm">{{ $totalVehicleTypes }}</span> Types (<span class="text-emerald-600 font-semibold">{{ $activeVehicleTypes }} Active</span>)
                        </div>
                        <a href="{{ route('master-data.vehicle-types.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center">
                            Manage Types &rarr;
                        </a>
                    </div>
                </div>

                <!-- Company Details Card -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs hover:border-blue-300 transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Center Profile</span>
                            <h3 class="text-base font-bold text-gray-900 mt-1">Company Details</h3>
                            <p class="text-xs text-gray-500 mt-1">Center name, business registration number, logo, phone, and tax identifiers.</p>
                        </div>
                        <div class="p-2.5 rounded-lg bg-blue-50 text-blue-600">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs text-gray-500">Auto Care Profile</span>
                        <a href="{{ route('master-data.company-details.edit') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center">
                            Edit Profile &rarr;
                        </a>
                    </div>
                </div>

                <!-- Integration Ready Architecture -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs hover:border-blue-300 transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">System Architecture</span>
                            <h3 class="text-base font-bold text-gray-900 mt-1">Invoice & Quotation System</h3>
                            <p class="text-xs text-gray-500 mt-1">Monolithic structure configured to smoothly integrate your existing billing and quotation code.</p>
                        </div>
                        <div class="p-2.5 rounded-lg bg-indigo-50 text-indigo-600">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs text-indigo-600 font-medium">Ready For Integration</span>
                        <span class="text-xs text-gray-400">Clean Architecture</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Audit Log Card -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold uppercase tracking-wider text-gray-700">Recent Activity</h2>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.activity-logs.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-semibold">View All</a>
                @endif
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs">
                @if($recentActivities->count() > 0)
                    <div class="flow-root">
                        <ul class="-mb-6">
                            @foreach($recentActivities as $log)
                                <li class="relative pb-6">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-3.5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex items-start space-x-3">
                                        <div class="h-7 w-7 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center ring-4 ring-white text-xs font-bold">
                                            @if($log->action === 'created')
                                                +
                                            @elseif($log->action === 'updated')
                                                ✎
                                            @elseif($log->action === 'login')
                                                →
                                            @else
                                                •
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-semibold text-gray-900 truncate">
                                                {{ $log->user ? $log->user->name : 'System' }}
                                                @if($log->module)
                                                    <span class="text-[10px] font-normal text-gray-500">({{ $log->module }})</span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-600 mt-0.5 line-clamp-2">{{ $log->description }}</p>
                                            <span class="text-[10px] text-gray-400 mt-1 block">{{ $log->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="text-center py-6">
                        <p class="text-xs text-gray-400">No activity recorded yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
