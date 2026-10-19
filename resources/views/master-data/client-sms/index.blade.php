@extends('layouts.app')

@section('title', 'Search Client SMS')

@section('content')
    <div x-data="{ 
        selectedCount: 0, 
        messageText: '', 
        selectAll: false,
        maxChars: 160,
        get charCount() { return this.messageText.length },
        get smsSegments() { return Math.ceil(Math.max(1, this.charCount) / 160) }
    }">
        <!-- Page Header -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Search Client SMS</h1>
                <p class="mt-1 text-sm text-gray-500">Target customer contacts for scheduled service alerts, reminders, and notifications.</p>
            </div>
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                    <svg class="w-3.5 h-3.5 mr-1 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    SMS Gateway Integration Staged
                </span>
            </div>
        </div>

        <!-- Safeguard Notice Banner -->
        <div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-4 text-amber-900 flex items-start space-x-3">
            <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="text-xs leading-relaxed">
                <span class="font-bold">Transmission Safety Policy:</span> Real SMS broadcasting is currently disabled. Live SMS dispatch will be unlocked once your SMS Provider API credentials (e.g., Twilio, Dialog, Mobitel) and customer communication consent policies are configured.
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Customer Search & Selection Table -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Search & Filters -->
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
                    <form method="GET" action="{{ route('master-data.client-sms.index') }}" class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Search by customer name, mobile, or vehicle plate..." 
                                class="block w-full pl-9 pr-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600"
                            />
                        </div>

                        <div class="w-full sm:w-48">
                            <select 
                                name="filter_type" 
                                class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 bg-white"
                            >
                                <option value="all">All Registered Clients</option>
                                <option value="due_service">Due for Next Service</option>
                                <option value="inactive_3m">Inactive (3+ Months)</option>
                            </select>
                        </div>

                        <button 
                            type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-gray-800 hover:bg-gray-900 rounded-lg transition"
                        >
                            Filter
                        </button>
                    </form>
                </div>

                <!-- Customer Selection Summary Bar -->
                <div class="bg-white px-5 py-3 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-600">Selected Recipients:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800" x-text="selectedCount + ' Clients'">
                            0 Clients
                        </span>
                    </div>
                    <button 
                        type="button" 
                        @click="selectedCount = 0; selectAll = false" 
                        class="text-xs font-semibold text-gray-500 hover:text-gray-700 transition"
                    >
                        Clear Selection
                    </button>
                </div>

                <!-- Simple Table Structure -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50/80">
                                <tr>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider w-12">
                                        <input 
                                            type="checkbox" 
                                            x-model="selectAll" 
                                            @change="selectedCount = selectAll ? 0 : 0" 
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        />
                                    </th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                        Customer Name
                                    </th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                        Mobile Number
                                    </th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                        Vehicle & Last Visit
                                    </th>
                                    <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">
                                        SMS Consent
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <!-- Empty state explaining that customer records are staged for upcoming Phase 2 Customer Module -->
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="max-w-md mx-auto text-center">
                                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-50 text-blue-600 mb-3">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-sm font-bold text-gray-900">No customer records in database yet.</h3>
                                            <p class="mt-1 text-xs text-gray-500 leading-relaxed">
                                                The Customer & Vehicle database is scheduled for Phase 2. Once customer profiles are entered or imported, they will appear here for filtering and broadcast targeting.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: SMS Composition & Live Preview Panel -->
            <div class="space-y-6">
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-xs">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gray-900 mb-4 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        Draft SMS Message
                    </h2>

                    <!-- Template Quick Pick -->
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Standard Templates</label>
                        <select 
                            @change="messageText = $event.target.value" 
                            class="block w-full py-2 px-3 text-xs text-gray-800 border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600"
                        >
                            <option value="">-- Choose a standard message --</option>
                            <option value="Dear Customer, your vehicle is due for periodic maintenance service at AutoCare. Call us to book your preferred bay.">Periodic Service Due</option>
                            <option value="Dear Customer, your vehicle service has been completed and is ready for collection at AutoCare. Thank you!">Service Ready For Collection</option>
                            <option value="Special seasonal auto care checkup available this week at AutoCare Center. Inquire today for details.">Seasonal Checkup Promo</option>
                        </select>
                    </div>

                    <!-- Message Body -->
                    <div class="mb-3">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Message Content</label>
                        <textarea 
                            x-model="messageText" 
                            rows="5" 
                            placeholder="Type customer notice message here..."
                            class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600"
                        ></textarea>
                    </div>

                    <!-- Metrics / Segments -->
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-4 bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                        <span>Characters: <strong class="text-gray-800" x-text="charCount">0</strong> / 160</span>
                        <span>Segments: <strong class="text-gray-800" x-text="smsSegments">1</strong></span>
                    </div>

                    <!-- Disabled Dispatch Button -->
                    <button 
                        type="button" 
                        disabled 
                        class="w-full flex items-center justify-center py-2.5 px-4 rounded-lg bg-gray-100 text-gray-400 font-semibold text-xs border border-gray-200 cursor-not-allowed"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Send SMS (Gateway Paused)
                    </button>
                    <p class="text-[11px] text-gray-400 text-center mt-2">Will be enabled upon SMS gateway binding.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
