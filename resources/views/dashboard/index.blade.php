@extends('layouts.app')

@section('title', 'Operations Dashboard')

@section('content')
<div x-data="{ showInvoiceWorkflowModal: false }">
    <!-- Dashboard Header -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">AutoCare Center Dashboard</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Daily workshop operations overview & center records. Today is <span class="font-medium text-gray-700">{{ now()->format('l, d F Y') }}</span>.
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

    <!-- Quick Actions Banner -->
    <div class="mb-8 bg-white border border-gray-200 rounded-xl p-5 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-gray-700">Quick Workshop Actions</h2>
                <p class="text-xs text-gray-500">Instant shortcuts for day-to-day shop floor workflows.</p>
            </div>
            <span class="text-xs bg-emerald-50 text-emerald-700 px-2.5 py-0.5 rounded-full font-semibold border border-emerald-200 flex items-center">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5"></span>
                Operational Shortcuts
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <!-- 1. New Job -->
            <a 
                href="{{ route('operations.vehicles.index') }}" 
                class="flex flex-col items-center justify-center p-3.5 rounded-lg border border-gray-200 bg-white hover:border-[#0B1849] hover:bg-slate-50/70 text-gray-800 transition group relative shadow-2xs"
                title="Search vehicle and create Job Card"
            >
                <div class="p-2 rounded-lg bg-[#0B1849]/5 text-[#0B1849] group-hover:bg-[#0B1849] group-hover:text-white transition mb-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-gray-900 group-hover:text-[#0B1849]">New Job</span>
                <span class="text-[10px] text-gray-500 font-medium mt-0.5">Vehicle & Job Card</span>
            </a>

            <!-- 2. New Customer -->
            <a 
                href="{{ route('operations.customers.create') }}" 
                class="flex flex-col items-center justify-center p-3.5 rounded-lg border border-gray-200 bg-white hover:border-[#0B1849] hover:bg-slate-50/70 text-gray-800 transition group relative shadow-2xs"
                title="Register a new customer profile"
            >
                <div class="p-2 rounded-lg bg-blue-50 text-blue-700 group-hover:bg-blue-600 group-hover:text-white transition mb-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-gray-900 group-hover:text-[#0B1849]">New Customer</span>
                <span class="text-[10px] text-gray-500 font-medium mt-0.5">Register Profile</span>
            </a>

            <!-- 3. New Quotation -->
            <a 
                href="{{ route('billing.quotations.create') }}" 
                class="flex flex-col items-center justify-center p-3.5 rounded-lg border border-gray-200 bg-white hover:border-[#0B1849] hover:bg-slate-50/70 text-gray-800 transition group relative shadow-2xs"
                title="Prepare a formal price estimation"
            >
                <div class="p-2 rounded-lg bg-amber-50 text-amber-700 group-hover:bg-amber-600 group-hover:text-white transition mb-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-gray-900 group-hover:text-[#0B1849]">New Quotation</span>
                <span class="text-[10px] text-gray-500 font-medium mt-0.5">Estimate & Quote</span>
            </a>

            <!-- 4. New Invoice -->
            @if(Auth::user()->isAdmin())
                <a 
                    href="{{ route('billing.invoices.create') }}" 
                    class="flex flex-col items-center justify-center p-3.5 rounded-lg border border-gray-200 bg-white hover:border-[#0B1849] hover:bg-slate-50/70 text-gray-800 transition group relative shadow-2xs"
                    title="Create manual invoice (Admin)"
                >
                    <div class="p-2 rounded-lg bg-emerald-50 text-emerald-700 group-hover:bg-emerald-600 group-hover:text-white transition mb-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-gray-900 group-hover:text-[#0B1849]">New Invoice</span>
                    <span class="text-[10px] text-emerald-700 font-medium mt-0.5">Manual Invoice (Admin)</span>
                </a>
            @else
                <button 
                    type="button" 
                    @click="showInvoiceWorkflowModal = true" 
                    class="flex flex-col items-center justify-center p-3.5 rounded-lg border border-gray-200 bg-white hover:border-[#0B1849] hover:bg-slate-50/70 text-gray-800 transition group relative shadow-2xs cursor-pointer"
                    title="Choose invoice generation workflow"
                >
                    <div class="p-2 rounded-lg bg-emerald-50 text-emerald-700 group-hover:bg-emerald-600 group-hover:text-white transition mb-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-gray-900 group-hover:text-[#0B1849]">New Invoice</span>
                    <span class="text-[10px] text-gray-500 font-medium mt-0.5">Billing Workflows</span>
                </button>
            @endif
        </div>
    </div>

    <!-- 5 Real Workshop Operation Metrics -->
    <div class="mb-8">
        <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-700 mb-3">Workshop Performance Metrics</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4">
            <!-- Today's Jobs -->
            <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-gray-500 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider">Today's Jobs</span>
                    <div class="p-2 rounded-lg bg-blue-50 text-blue-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-xl sm:text-2xl font-bold text-gray-900">{{ $todayJobs }}</div>
                    <div class="text-[11px] text-gray-400 mt-1">Logged vehicles today</div>
                </div>
            </div>

            <!-- Pending Jobs -->
            <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-gray-500 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider">Pending Jobs</span>
                    <div class="p-2 rounded-lg bg-amber-50 text-amber-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-xl sm:text-2xl font-bold text-amber-600">{{ $pendingJobs }}</div>
                    <div class="text-[11px] text-gray-400 mt-1">In progress on floor</div>
                </div>
            </div>

            <!-- Completed Jobs -->
            <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-gray-500 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider">Completed Jobs</span>
                    <div class="p-2 rounded-lg bg-emerald-50 text-emerald-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-xl sm:text-2xl font-bold text-emerald-600">{{ $completedJobs }}</div>
                    <div class="text-[11px] text-gray-400 mt-1">Ready for collection today</div>
                </div>
            </div>

            <!-- Today's Sales -->
            <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-gray-500 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider">Today's Sales</span>
                    <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-lg sm:text-xl xl:text-2xl font-bold text-gray-900 truncate" title="LKR {{ number_format($todaySales, 2) }}">
                        LKR {{ number_format($todaySales, 2) }}
                    </div>
                    <div class="text-[11px] text-gray-400 mt-1">Recognized sales today</div>
                </div>
            </div>

            <!-- Pending Payments -->
            <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-gray-500 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider">Pending Payments</span>
                    <div class="p-2 rounded-lg bg-rose-50 text-rose-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-lg sm:text-xl xl:text-2xl font-bold text-rose-600 truncate" title="LKR {{ number_format($pendingPayments, 2) }}">
                        LKR {{ number_format($pendingPayments, 2) }}
                    </div>
                    <div class="text-[11px] text-gray-400 mt-1">Outstanding receivables</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Master Data Setup Status & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Master Data Status Cards -->
        <div class="lg:col-span-2 space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-gray-700">Master Data & Center Configuration</h2>
            
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

                <!-- Operations & Invoicing Overview Card -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs hover:border-blue-300 transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Operations & Invoicing</span>
                            <h3 class="text-base font-bold text-gray-900 mt-1">Job Cards & Billing</h3>
                            <p class="text-xs text-gray-500 mt-1">Track workshop job cards, issue parts with FIFO inventory, and generate invoices.</p>
                        </div>
                        <div class="p-2.5 rounded-lg bg-indigo-50 text-indigo-600">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                        <a href="{{ route('operations.job-cards.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                            Job Cards &rarr;
                        </a>
                        <a href="{{ route('billing.invoices.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                            Invoices &rarr;
                        </a>
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
                                                &rarr;
                                            @else
                                                &bull;
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

    <!-- Staff Invoice Workflow Choice Modal -->
    <div 
        x-show="showInvoiceWorkflowModal" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
        aria-labelledby="modal-title" 
        role="dialog" 
        aria-modal="true"
    >
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div 
                x-show="showInvoiceWorkflowModal" 
                x-transition:enter="ease-out duration-200" 
                x-transition:enter-start="opacity-0" 
                x-transition:enter-end="opacity-100" 
                x-transition:leave="ease-in duration-150" 
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0" 
                class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" 
                @click="showInvoiceWorkflowModal = false"
            ></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div 
                x-show="showInvoiceWorkflowModal" 
                x-transition:enter="ease-out duration-200" 
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-150" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full max-w-[calc(100vw-2rem)] border border-gray-200 max-h-[85vh] overflow-y-auto"
            >
                <div class="bg-[#0B1849] px-6 py-4 flex items-center justify-between text-white">
                    <h3 class="text-base font-bold" id="modal-title">Create / Generate Invoice</h3>
                    <button type="button" @click="showInvoiceWorkflowModal = false" class="text-gray-300 hover:text-white">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <p class="text-xs text-gray-500">
                        Invoices must be created from an authorized business workflow to ensure accurate inventory deductions and prevent duplicate records. Choose an option:
                    </p>

                    <div class="space-y-3">
                        <!-- Option 1: Job Card Invoice -->
                        <a 
                            href="{{ route('operations.job-cards.index', ['status' => 'completed']) }}" 
                            class="flex items-start p-3.5 border border-gray-200 rounded-lg hover:border-[#0B1849] hover:bg-slate-50 transition group"
                        >
                            <div class="p-2 rounded-lg bg-blue-50 text-blue-700 group-hover:bg-[#0B1849] group-hover:text-white transition mr-3 shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 group-hover:text-[#0B1849]">From Completed Job Card</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Generate an itemized invoice from completed workshop service and issued parts.</p>
                            </div>
                        </a>

                        <!-- Option 2: Direct Sale POS -->
                        <a 
                            href="{{ route('operations.direct-sales.index') }}" 
                            class="flex items-start p-3.5 border border-gray-200 rounded-lg hover:border-[#0B1849] hover:bg-slate-50 transition group"
                        >
                            <div class="p-2 rounded-lg bg-emerald-50 text-emerald-700 group-hover:bg-[#0B1849] group-hover:text-white transition mr-3 shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 group-hover:text-[#0B1849]">Counter / Direct Sale (POS)</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Sell spare parts or retail items over the counter with immediate invoice & stock deduction.</p>
                            </div>
                        </a>

                        <!-- Option 3: Convert Quotation -->
                        <a 
                            href="{{ route('billing.quotations.index', ['status' => 'accepted']) }}" 
                            class="flex items-start p-3.5 border border-gray-200 rounded-lg hover:border-[#0B1849] hover:bg-slate-50 transition group"
                        >
                            <div class="p-2 rounded-lg bg-amber-50 text-amber-700 group-hover:bg-[#0B1849] group-hover:text-white transition mr-3 shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 group-hover:text-[#0B1849]">From Accepted Quotation</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Convert an accepted customer estimate directly into a billed invoice.</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex justify-end">
                    <button 
                        type="button" 
                        @click="showInvoiceWorkflowModal = false" 
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-bold uppercase rounded-lg transition"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
