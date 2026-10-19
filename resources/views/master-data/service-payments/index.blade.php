@extends('layouts.app')

@section('title', 'Manage Service Payment')

@section('content')
    <!-- Page Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Manage Service Payment</h1>
            <p class="mt-1 text-sm text-gray-500">Service pricing structure, technician commissions, and billing definitions.</p>
        </div>
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                <span class="w-2 h-2 bg-amber-500 rounded-full mr-2"></span>
                Pending Business Rule Confirmation
            </span>
        </div>
    </div>

    <!-- Structured Placeholder Content Card -->
    <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-xs max-w-3xl">
        <div class="flex items-start space-x-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl flex-shrink-0">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Service Payment Parameters</h2>
                <p class="mt-1 text-sm text-gray-600 leading-relaxed">
                    The payment calculation framework, commission percentages, and customer billing rules for AutoCare System are currently pending confirmation from management.
                </p>

                <div class="mt-6 rounded-lg bg-gray-50 border border-gray-200 p-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">Planned Module Capabilities:</h3>
                    <ul class="mt-2 text-xs text-gray-600 space-y-2 list-disc list-inside">
                        <li>Fixed vs hourly service rate cards categorized by vehicle type (e.g. Car vs SUV vs Lorry).</li>
                        <li>Technician and workshop staff commission distribution percentages.</li>
                        <li>Discount policies, promotional rate caps, and tax inclusive/exclusive preferences.</li>
                        <li>Direct bridge to the upcoming Quotation and Invoice module.</li>
                    </ul>
                </div>

                <div class="mt-6 flex items-center space-x-3 text-xs text-gray-500">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Architecture and database tables are staged to integrate seamlessly once your business formulas are confirmed.</span>
                </div>
            </div>
        </div>
    </div>
@endsection
