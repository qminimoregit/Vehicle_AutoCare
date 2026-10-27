@extends('layouts.app')

@section('title', 'Appointment Management')

@section('content')
<div 
    x-data="{
        // Modals
        showNewModal: false,
        showDetailsModal: false,
        showEditModal: false,
        selectedAppointment: null,

        // Calendar state
        viewMode: 'month', // 'month', 'week', 'day'
        currentDate: new Date(),
        events: [],
        loadingEvents: false,

        // New Appointment Form state
        newVehicleNumber: '',
        newCustomerName: '',
        newMobileNumber: '',
        newVehicleTypeId: '',
        newAppointmentDate: '{{ now()->toDateString() }}',
        newAppointmentTime: '09:00 AM',
        newServiceId: '',
        newNotes: '',
        vehicleLookupMessage: '',
        vehicleLookupFound: false,

        // Format Date helpers
        pad(n) { return n < 10 ? '0' + n : n; },
        formatYMD(d) {
            return d.getFullYear() + '-' + this.pad(d.getMonth() + 1) + '-' + this.pad(d.getDate());
        },

        init() {
            this.fetchEvents();
        },

        async fetchEvents() {
            this.loadingEvents = true;
            try {
                let res = await fetch('{{ route('appointments.events') }}');
                let data = await res.json();
                this.events = data;
            } catch (e) {
                console.error('Failed to load appointment events', e);
            } finally {
                this.loadingEvents = false;
            }
        },

        // Navigation
        prev() {
            let d = new Date(this.currentDate);
            if (this.viewMode === 'month') {
                d.setMonth(d.getMonth() - 1);
            } else if (this.viewMode === 'week') {
                d.setDate(d.getDate() - 7);
            } else if (this.viewMode === 'day') {
                d.setDate(d.getDate() - 1);
            }
            this.currentDate = d;
        },

        next() {
            let d = new Date(this.currentDate);
            if (this.viewMode === 'month') {
                d.setMonth(d.getMonth() + 1);
            } else if (this.viewMode === 'week') {
                d.setDate(d.getDate() + 7);
            } else if (this.viewMode === 'day') {
                d.setDate(d.getDate() + 1);
            }
            this.currentDate = d;
        },

        today() {
            this.currentDate = new Date();
        },

        getTitle() {
            let d = this.currentDate;
            let monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            if (this.viewMode === 'month') {
                return monthNames[d.getMonth()] + ' ' + d.getFullYear();
            } else if (this.viewMode === 'week') {
                let startOfWeek = new Date(d);
                startOfWeek.setDate(d.getDate() - d.getDay());
                let endOfWeek = new Date(startOfWeek);
                endOfWeek.setDate(startOfWeek.getDate() + 6);
                return startOfWeek.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ' – ' + endOfWeek.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            } else {
                return d.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
            }
        },

        // Month Grid Computation
        getMonthDays() {
            let year = this.currentDate.getFullYear();
            let month = this.currentDate.getMonth();
            let firstDay = new Date(year, month, 1);
            let lastDay = new Date(year, month + 1, 0);

            let days = [];
            let startDayOfWeek = firstDay.getDay(); // 0 is Sun

            // Previous month trailing days
            let prevMonthLastDay = new Date(year, month, 0).getDate();
            for (let i = startDayOfWeek - 1; i >= 0; i--) {
                let d = new Date(year, month - 1, prevMonthLastDay - i);
                days.push({
                    date: d,
                    dateString: this.formatYMD(d),
                    dayNumber: d.getDate(),
                    isCurrentMonth: false,
                    isToday: this.formatYMD(d) === this.formatYMD(new Date())
                });
            }

            // Current month days
            for (let i = 1; i <= lastDay.getDate(); i++) {
                let d = new Date(year, month, i);
                days.push({
                    date: d,
                    dateString: this.formatYMD(d),
                    dayNumber: i,
                    isCurrentMonth: true,
                    isToday: this.formatYMD(d) === this.formatYMD(new Date())
                });
            }

            // Next month days to fill 35 or 42 grid
            let totalSlots = days.length <= 35 ? 35 : 42;
            let remaining = totalSlots - days.length;
            for (let i = 1; i <= remaining; i++) {
                let d = new Date(year, month + 1, i);
                days.push({
                    date: d,
                    dateString: this.formatYMD(d),
                    dayNumber: i,
                    isCurrentMonth: false,
                    isToday: this.formatYMD(d) === this.formatYMD(new Date())
                });
            }

            return days;
        },

        // Week Days Computation
        getWeekDays() {
            let d = new Date(this.currentDate);
            let startOfWeek = new Date(d);
            startOfWeek.setDate(d.getDate() - d.getDay());

            let days = [];
            for (let i = 0; i < 7; i++) {
                let cur = new Date(startOfWeek);
                cur.setDate(startOfWeek.getDate() + i);
                days.push({
                    date: cur,
                    dateString: this.formatYMD(cur),
                    dayName: cur.toLocaleDateString('en-US', { weekday: 'short' }),
                    dayNumber: cur.getDate(),
                    isToday: this.formatYMD(cur) === this.formatYMD(new Date())
                });
            }
            return days;
        },

        // Filter events for a date
        getEventsForDate(dateStr) {
            return this.events.filter(e => e.date === dateStr);
        },

        // Click date to create appointment
        clickDate(dateStr) {
            this.newAppointmentDate = dateStr;
            this.showNewModal = true;
        },

        // Mobile responsive day click handler
        handleDayClick(day) {
            let dayEvents = this.getEventsForDate(day.dateString);
            if (window.innerWidth < 640 && dayEvents.length > 0) {
                this.currentDate = new Date(day.date);
                this.viewMode = 'day';
            } else {
                this.clickDate(day.dateString);
            }
        },

        // Click event to view details
        viewEvent(event) {
            this.selectedAppointment = event;
            this.showDetailsModal = true;
        },

        // Status badge colors
        getStatusClasses(status) {
            switch (status) {
                case 'scheduled': return 'bg-blue-100 text-blue-800 border-blue-200';
                case 'confirmed': return 'bg-teal-100 text-teal-800 border-teal-200';
                case 'arrived': return 'bg-purple-100 text-purple-800 border-purple-200';
                case 'in_service': return 'bg-amber-100 text-amber-800 border-amber-200';
                case 'completed': return 'bg-emerald-100 text-emerald-800 border-emerald-200';
                case 'cancelled': return 'bg-red-100 text-red-800 border-red-200';
                case 'no_show': return 'bg-gray-100 text-gray-800 border-gray-200';
                default: return 'bg-gray-100 text-gray-800 border-gray-200';
            }
        },

        // Live search vehicle by number
        async lookupVehicle(vehicleNum) {
            let trimmed = (vehicleNum || '').trim();
            if (!trimmed || trimmed.length < 3) {
                this.vehicleLookupMessage = '';
                this.vehicleLookupFound = false;
                return;
            }

            try {
                let res = await fetch('{{ route('operations.vehicles.search') }}?number=' + encodeURIComponent(trimmed));
                let data = await res.json();
                if (data.exists && data.vehicle) {
                    this.vehicleLookupFound = true;
                    this.vehicleLookupMessage = 'Existing vehicle found. Customer details loaded.';
                    if (data.customer) {
                        this.newCustomerName = data.customer.customer_name || this.newCustomerName;
                        this.newMobileNumber = data.customer.phone || this.newMobileNumber;
                    }
                    if (data.vehicle.vehicle_type_id) {
                        this.newVehicleTypeId = data.vehicle.vehicle_type_id;
                    }
                } else {
                    this.vehicleLookupFound = false;
                    this.vehicleLookupMessage = 'New vehicle. Please enter customer details.';
                }
            } catch (err) {
                console.error('Vehicle lookup error', err);
            }
        }
    }"
    class="max-w-7xl mx-auto space-y-6"
>
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight flex items-center">
                <svg class="w-6 h-6 mr-2.5 text-[#0B1849]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Appointment Management
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Schedule and manage customer service appointments, monitor arrivals, and initiate job cards seamlessly.
            </p>
        </div>

        <div>
            <button 
                type="button" 
                @click="showNewModal = true"
                class="inline-flex items-center justify-center px-4 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white font-bold text-sm rounded-lg shadow-xs transition cursor-pointer min-h-[44px]"
            >
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Appointment
            </button>
        </div>
    </div>

    <!-- 1. APPOINTMENT LIST & QUICK FILTERS -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        <!-- List Header & Filter Tabs -->
        <div class="px-5 py-4 border-b border-gray-100 bg-slate-50/60 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <!-- Filter Tabs -->
            <div class="flex items-center space-x-2">
                <a 
                    href="{{ route('appointments.index', ['filter' => 'today']) }}" 
                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition {{ $filter === 'today' ? 'bg-[#0B1849] text-white shadow-xs' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}"
                >
                    Today ({{ $counts['today'] }})
                </a>
                <a 
                    href="{{ route('appointments.index', ['filter' => 'upcoming']) }}" 
                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition {{ $filter === 'upcoming' ? 'bg-[#0B1849] text-white shadow-xs' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}"
                >
                    Upcoming ({{ $counts['upcoming'] }})
                </a>
                <a 
                    href="{{ route('appointments.index', ['filter' => 'all']) }}" 
                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition {{ $filter === 'all' ? 'bg-[#0B1849] text-white shadow-xs' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}"
                >
                    All ({{ $counts['all'] }})
                </a>
            </div>

            <!-- Search Form -->
            <form action="{{ route('appointments.index') }}" method="GET" class="w-full sm:w-72">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search }}"
                        placeholder="Search vehicle, customer, or phone..." 
                        class="block w-full py-2 pl-9 pr-3 text-xs text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[36px]"
                    >
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </form>
        </div>

        <!-- Appointment Table -->
        <div class="responsive-table-wrapper">
            <table class="min-w-full divide-y divide-gray-100 text-left text-xs sm:text-sm">
                <thead class="bg-gray-50/50 text-gray-500 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-5 py-3">Scheduled Time</th>
                        <th class="px-4 py-3">Vehicle Number</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Service</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-800">
                    @forelse($appointments as $apt)
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- Time & Date -->
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="font-bold text-gray-900">{{ $apt->appointment_time }}</div>
                                <div class="text-[11px] text-gray-500">{{ $apt->appointment_date ? $apt->appointment_date->format('M d, Y') : '' }}</div>
                            </td>

                            <!-- Vehicle -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="font-black text-gray-900 tracking-tight">{{ $apt->vehicle_number }}</div>
                                <div class="text-[11px] text-gray-500">{{ $apt->vehicleType?->name ?? 'Vehicle' }}</div>
                            </td>

                            <!-- Customer -->
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-gray-900">{{ $apt->customer_name }}</div>
                                <div class="text-[11px] text-gray-500 font-mono">{{ $apt->mobile_number }}</div>
                            </td>

                            <!-- Service -->
                            <td class="px-4 py-3.5">
                                <div class="font-medium text-gray-900">{{ $apt->service?->name ?? $apt->service_name ?? 'General Service' }}</div>
                                @if($apt->notes)
                                    <div class="text-[11px] text-gray-500 italic truncate max-w-xs">{{ $apt->notes }}</div>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                @if($apt->status === 'scheduled')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800">Scheduled</span>
                                @elseif($apt->status === 'confirmed')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-teal-100 text-teal-800">Confirmed</span>
                                @elseif($apt->status === 'arrived')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-100 text-purple-800">Arrived</span>
                                @elseif($apt->status === 'in_service')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">In Service</span>
                                @elseif($apt->status === 'completed')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Completed</span>
                                @elseif($apt->status === 'cancelled')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-100 text-red-800">Cancelled</span>
                                @elseif($apt->status === 'no_show')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-gray-100 text-gray-800">No Show</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center space-x-1.5">
                                    <!-- View Details Button -->
                                    <button 
                                        type="button" 
                                        @click="viewEvent({
                                            id: {{ $apt->id }},
                                            appointment_number: '{{ $apt->appointment_number }}',
                                            title: '{{ $apt->appointment_time }} – {{ $apt->vehicle_number }} – {{ addslashes($apt->service?->name ?? $apt->service_name ?? 'Service') }}',
                                            date: '{{ $apt->appointment_date ? $apt->appointment_date->format('Y-m-d') : '' }}',
                                            time: '{{ $apt->appointment_time }}',
                                            vehicle_number: '{{ $apt->vehicle_number }}',
                                            customer_name: '{{ addslashes($apt->customer_name) }}',
                                            mobile_number: '{{ $apt->mobile_number }}',
                                            service_name: '{{ addslashes($apt->service?->name ?? $apt->service_name ?? 'General Service') }}',
                                            service_id: {{ $apt->service_id ?? 'null' }},
                                            vehicle_type_name: '{{ addslashes($apt->vehicleType?->name ?? 'Vehicle') }}',
                                            vehicle_type_id: {{ $apt->vehicle_type_id ?? 'null' }},
                                            status: '{{ $apt->status }}',
                                            notes: '{{ addslashes($apt->notes ?? '') }}',
                                            job_card_id: {{ $apt->job_card_id ?? 'null' }},
                                            job_card_number: '{{ $apt->jobCard?->job_number ?? '' }}',
                                            job_card_url: '{{ $apt->job_card_id ? route('operations.job-cards.show', $apt->job_card_id) : '' }}'
                                        })"
                                        class="px-2.5 py-1 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded shadow-xs transition cursor-pointer"
                                    >
                                        View
                                    </button>

                                    <!-- Customer Arrived Action -->
                                    @if(in_array($apt->status, ['scheduled', 'confirmed']))
                                        <form action="{{ route('appointments.arrive', $apt) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button 
                                                type="submit" 
                                                class="px-2.5 py-1 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded shadow-xs transition cursor-pointer"
                                                title="Mark customer as arrived"
                                            >
                                                Arrived
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Create Job Card or Open Job Card -->
                                    @if($apt->job_card_id && $apt->jobCard)
                                        <a 
                                            href="{{ route('operations.job-cards.show', $apt->jobCard) }}" 
                                            class="px-2.5 py-1 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-bold rounded shadow-xs transition"
                                        >
                                            Open Job Card
                                        </a>
                                    @elseif($apt->status !== 'cancelled')
                                        <form action="{{ route('appointments.create-job-card', $apt) }}" method="POST" class="inline">
                                            @csrf
                                            <button 
                                                type="submit" 
                                                class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded shadow-xs transition cursor-pointer"
                                                title="Create Job Card from this appointment"
                                            >
                                                Create Job Card
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Cancel Button -->
                                    @if(!in_array($apt->status, ['cancelled', 'completed', 'in_service']))
                                        <form action="{{ route('appointments.cancel', $apt) }}" method="POST" class="inline" onsubmit="return confirm('Cancel this appointment?');">
                                            @csrf
                                            @method('PATCH')
                                            <button 
                                                type="submit" 
                                                class="p-1 text-gray-400 hover:text-red-600 transition"
                                                title="Cancel Appointment"
                                            >
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-xs sm:text-sm text-gray-400">
                                No appointments found for the selected filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($appointments->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 bg-slate-50/50">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>

    <!-- 2. APPOINTMENT CALENDAR CARD -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        <!-- Calendar Controls Header -->
        <div class="px-5 py-4 border-b border-gray-100 bg-slate-50/70 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center space-x-2">
                <!-- Navigation Buttons -->
                <div class="inline-flex rounded-lg border border-gray-200 bg-white p-0.5 shadow-xs">
                    <button 
                        type="button" 
                        @click="prev()"
                        class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition cursor-pointer"
                        title="Previous"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button 
                        type="button" 
                        @click="today()"
                        class="px-2.5 py-1 text-xs font-bold text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-md transition cursor-pointer"
                    >
                        Today
                    </button>
                    <button 
                        type="button" 
                        @click="next()"
                        class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition cursor-pointer"
                        title="Next"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <!-- Current Period Title -->
                <h2 class="text-base sm:text-lg font-black text-gray-900 ml-2" x-text="getTitle()"></h2>
            </div>

            <!-- View Mode Switcher -->
            <div class="inline-flex rounded-lg border border-gray-200 bg-gray-100 p-0.5 shadow-xs self-stretch md:self-auto">
                <button 
                    type="button" 
                    @click="viewMode = 'month'"
                    :class="viewMode === 'month' ? 'bg-white text-[#0B1849] font-bold shadow-xs' : 'text-gray-600 font-medium hover:text-gray-900'"
                    class="px-3 py-1.5 text-xs rounded-md transition cursor-pointer flex-1 md:flex-none text-center"
                >
                    Month
                </button>
                <button 
                    type="button" 
                    @click="viewMode = 'week'"
                    :class="viewMode === 'week' ? 'bg-white text-[#0B1849] font-bold shadow-xs' : 'text-gray-600 font-medium hover:text-gray-900'"
                    class="px-3 py-1.5 text-xs rounded-md transition cursor-pointer flex-1 md:flex-none text-center"
                >
                    Week
                </button>
                <button 
                    type="button" 
                    @click="viewMode = 'day'"
                    :class="viewMode === 'day' ? 'bg-white text-[#0B1849] font-bold shadow-xs' : 'text-gray-600 font-medium hover:text-gray-900'"
                    class="px-3 py-1.5 text-xs rounded-md transition cursor-pointer flex-1 md:flex-none text-center"
                >
                    Day
                </button>
            </div>
        </div>

        <!-- CALENDAR VIEW 1: MONTH VIEW -->
        <div x-show="viewMode === 'month'" class="p-2 sm:p-5">
            <!-- Day of Week Headers -->
            <div class="grid grid-cols-7 gap-1 text-center font-bold text-xs uppercase tracking-wider text-gray-500 pb-2 border-b border-gray-100">
                <div><span class="sm:hidden">S</span><span class="hidden sm:inline">Sun</span></div>
                <div><span class="sm:hidden">M</span><span class="hidden sm:inline">Mon</span></div>
                <div><span class="sm:hidden">T</span><span class="hidden sm:inline">Tue</span></div>
                <div><span class="sm:hidden">W</span><span class="hidden sm:inline">Wed</span></div>
                <div><span class="sm:hidden">T</span><span class="hidden sm:inline">Thu</span></div>
                <div><span class="sm:hidden">F</span><span class="hidden sm:inline">Fri</span></div>
                <div><span class="sm:hidden">S</span><span class="hidden sm:inline">Sat</span></div>
            </div>

            <!-- Month Calendar Grid -->
            <div class="grid grid-cols-7 gap-1 sm:gap-2 mt-2">
                <template x-for="(day, idx) in getMonthDays()" :key="idx">
                    <div 
                        @click="handleDayClick(day)"
                        class="min-h-[58px] sm:min-h-[110px] p-1 sm:p-2 rounded-lg border transition flex flex-col justify-between cursor-pointer group"
                        :class="[
                            day.isCurrentMonth ? 'bg-white hover:bg-slate-50/80 border-gray-100' : 'bg-slate-50/50 text-gray-400 border-gray-50',
                            day.isToday ? 'ring-2 ring-[#0B1849] bg-blue-50/30' : ''
                        ]"
                    >
                        <!-- Date Number & Add Button -->
                        <div class="flex items-center justify-between">
                            <span 
                                class="text-[11px] sm:text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full"
                                :class="day.isToday ? 'bg-[#0B1849] text-white' : (day.isCurrentMonth ? 'text-gray-800' : 'text-gray-400')"
                                x-text="day.dayNumber"
                            ></span>
                            <span class="hidden sm:inline opacity-0 group-hover:opacity-100 text-[10px] text-gray-400 hover:text-[#0B1849] font-bold transition">
                                + Add
                            </span>
                        </div>

                        <!-- Mobile Event Indicator Dots (< sm) -->
                        <div class="flex sm:hidden flex-wrap items-center justify-center gap-1 my-auto">
                            <template x-for="event in getEventsForDate(day.dateString).slice(0, 3)" :key="event.id">
                                <span class="w-1.5 h-1.5 rounded-full" :class="event.status === 'completed' ? 'bg-emerald-500' : (event.status === 'cancelled' ? 'bg-red-400' : 'bg-[#0B1849]')"></span>
                            </template>
                            <template x-if="getEventsForDate(day.dateString).length > 3">
                                <span class="text-[8px] font-black text-gray-500" x-text="'+' + (getEventsForDate(day.dateString).length - 3)"></span>
                            </template>
                        </div>

                        <!-- Desktop Event Chips (> sm) -->
                        <div class="hidden sm:block space-y-1 overflow-y-auto max-h-[75px] mt-1 thin-scrollbar">
                            <template x-for="event in getEventsForDate(day.dateString)" :key="event.id">
                                <div 
                                    @click.stop="viewEvent(event)"
                                    class="px-1.5 py-0.5 rounded text-[10px] font-semibold border truncate transition cursor-pointer hover:shadow-xs"
                                    :class="getStatusClasses(event.status)"
                                    :title="event.title"
                                >
                                    <span x-text="event.title"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- CALENDAR VIEW 2: WEEK VIEW -->
        <div x-show="viewMode === 'week'" class="p-3 sm:p-5" style="display: none;">
            <div class="grid grid-cols-1 md:grid-cols-7 gap-3">
                <template x-for="(wDay, wIdx) in getWeekDays()" :key="wIdx">
                    <div 
                        class="bg-white border rounded-lg p-3 flex flex-col min-h-[300px]"
                        :class="wDay.isToday ? 'border-[#0B1849] ring-1 ring-[#0B1849]' : 'border-gray-200'"
                    >
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <div>
                                <div class="text-[10px] uppercase font-bold text-gray-400" x-text="wDay.dayName"></div>
                                <div class="text-sm font-black text-gray-900" x-text="wDay.dayNumber"></div>
                            </div>
                            <button 
                                type="button" 
                                @click="clickDate(wDay.dateString)"
                                class="p-1 text-gray-400 hover:text-[#0B1849] text-xs font-bold"
                                title="Add appointment"
                            >
                                + Add
                            </button>
                        </div>

                        <!-- Events for week day -->
                        <div class="space-y-2 mt-2 flex-1 overflow-y-auto thin-scrollbar">
                            <template x-for="event in getEventsForDate(wDay.dateString)" :key="event.id">
                                <div 
                                    @click="viewEvent(event)"
                                    class="p-2 rounded-lg border text-xs cursor-pointer hover:shadow-xs transition space-y-1"
                                    :class="getStatusClasses(event.status)"
                                >
                                    <div class="font-bold flex items-center justify-between">
                                        <span x-text="event.time"></span>
                                        <span class="text-[9px] uppercase px-1 py-0.2 rounded font-mono" x-text="event.status"></span>
                                    </div>
                                    <div class="font-bold text-gray-900" x-text="event.vehicle_number"></div>
                                    <div class="text-[11px] truncate" x-text="event.service_name"></div>
                                    <div class="text-[10px] text-gray-500 truncate" x-text="event.customer_name"></div>
                                </div>
                            </template>
                            <template x-if="getEventsForDate(wDay.dateString).length === 0">
                                <div class="text-center py-8 text-gray-400 text-xs italic">
                                    No appointments
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- CALENDAR VIEW 3: DAY VIEW -->
        <div x-show="viewMode === 'day'" class="p-3 sm:p-5" style="display: none;">
            <div class="max-w-2xl mx-auto space-y-3">
                <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                    <div>
                        <h3 class="text-base font-bold text-gray-900" x-text="getTitle()"></h3>
                        <p class="text-xs text-gray-500">Scheduled appointments for this day</p>
                    </div>
                    <button 
                        type="button" 
                        @click="clickDate(formatYMD(currentDate))"
                        class="px-3 py-1.5 bg-[#0B1849] text-white text-xs font-bold rounded-md hover:bg-[#15286e]"
                    >
                        + Add Appointment
                    </button>
                </div>

                <!-- Event list for the selected day -->
                <div class="space-y-2.5">
                    <template x-for="event in getEventsForDate(formatYMD(currentDate))" :key="event.id">
                        <div 
                            @click="viewEvent(event)"
                            class="p-3 sm:p-4 rounded-xl border flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-4 cursor-pointer hover:shadow-xs transition"
                            :class="getStatusClasses(event.status)"
                        >
                            <div class="flex items-start sm:items-center space-x-3">
                                <div class="text-center font-bold px-2.5 sm:px-3 py-1.5 bg-white/80 rounded-lg border border-gray-200/50 shadow-2xs shrink-0">
                                    <span class="text-xs text-gray-900 block" x-text="event.time"></span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-sm text-gray-900 flex flex-wrap items-center gap-1.5">
                                        <span x-text="event.vehicle_number"></span>
                                        <span class="text-xs font-medium text-gray-500" x-text="'(' + event.vehicle_type_name + ')'"></span>
                                    </div>
                                    <div class="text-xs text-gray-700 mt-0.5">
                                        <span class="font-semibold" x-text="event.service_name"></span>
                                        <span class="text-gray-400 mx-1">•</span>
                                        <span x-text="event.customer_name"></span>
                                        <span class="text-gray-400" x-text="'(' + event.mobile_number + ')'"></span>
                                    </div>
                                    <template x-if="event.notes">
                                        <div class="text-[11px] text-gray-500 italic mt-0.5" x-text="event.notes"></div>
                                    </template>
                                </div>
                            </div>

                            <div class="flex items-center justify-between sm:justify-end space-x-2 pt-2 sm:pt-0 border-t sm:border-t-0 border-black/5 shrink-0">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider" x-text="event.status"></span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </template>
                    <template x-if="getEventsForDate(formatYMD(currentDate)).length === 0">
                        <div class="text-center py-12 text-gray-400 text-sm">
                            No appointments scheduled for this date. Click "+ Add Appointment" above to book one.
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 1: NEW APPOINTMENT REGISTRATION -->
    <div 
        x-show="showNewModal" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" @click="showNewModal = false"></div>

            <div class="relative bg-white rounded-2xl w-full max-w-[calc(100vw-2rem)] sm:max-w-lg p-4 sm:p-6 shadow-xl border border-gray-200 space-y-4 max-h-[88vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-base font-bold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#0B1849]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Book New Appointment
                    </h3>
                    <button type="button" @click="showNewModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('appointments.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Vehicle Number with Real-time Auto-lookup -->
                    <div>
                        <label for="new_vehicle_number" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Vehicle Number <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="vehicle_number" 
                            id="new_vehicle_number" 
                            x-model="newVehicleNumber"
                            @blur="lookupVehicle(newVehicleNumber)"
                            placeholder="e.g. BFH-4242 or CAB-1234"
                            class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white font-mono font-bold uppercase min-h-[44px]"
                            required
                        >
                        <!-- Lookup feedback message -->
                        <div x-show="vehicleLookupMessage" class="mt-1 text-xs" :class="vehicleLookupFound ? 'text-emerald-700 font-semibold' : 'text-gray-500'">
                            <span x-text="vehicleLookupMessage"></span>
                        </div>
                    </div>

                    <!-- Customer Name & Mobile Number -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="new_customer_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Customer Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="customer_name" 
                                id="new_customer_name" 
                                x-model="newCustomerName"
                                placeholder="e.g. John Silva"
                                class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                                required
                            >
                        </div>
                        <div>
                            <label for="new_mobile_number" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Mobile Number <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="mobile_number" 
                                id="new_mobile_number" 
                                x-model="newMobileNumber"
                                placeholder="e.g. 0771234567"
                                class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                                required
                            >
                        </div>
                    </div>

                    <!-- Vehicle Type & Service Selection -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="new_vehicle_type_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Vehicle Type <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="vehicle_type_id" 
                                id="new_vehicle_type_id" 
                                x-model="newVehicleTypeId"
                                class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                                required
                            >
                                <option value="">-- Choose Type --</option>
                                @foreach($vehicleTypes as $vt)
                                    <option value="{{ $vt->id }}">{{ $vt->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="new_service_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Service
                            </label>
                            <x-searchable-select 
                                name="service_id" 
                                :endpoint="route('search.services')" 
                                placeholder="-- Select Service (Optional) --" 
                                :clearable="true" 
                            />
                        </div>
                    </div>

                    <!-- Date & Time -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="new_appointment_date" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Appointment Date <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="appointment_date" 
                                id="new_appointment_date" 
                                x-model="newAppointmentDate"
                                class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                                required
                            >
                        </div>
                        <div>
                            <label for="new_appointment_time" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Appointment Time <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="appointment_time" 
                                id="new_appointment_time" 
                                x-model="newAppointmentTime"
                                class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                                required
                            >
                                <option value="08:00 AM">08:00 AM</option>
                                <option value="08:30 AM">08:30 AM</option>
                                <option value="09:00 AM" selected>09:00 AM</option>
                                <option value="09:30 AM">09:30 AM</option>
                                <option value="10:00 AM">10:00 AM</option>
                                <option value="10:30 AM">10:30 AM</option>
                                <option value="11:00 AM">11:00 AM</option>
                                <option value="11:30 AM">11:30 AM</option>
                                <option value="12:00 PM">12:00 PM</option>
                                <option value="12:30 PM">12:30 PM</option>
                                <option value="01:00 PM">01:00 PM</option>
                                <option value="01:30 PM">01:30 PM</option>
                                <option value="02:00 PM">02:00 PM</option>
                                <option value="02:30 PM">02:30 PM</option>
                                <option value="03:00 PM">03:00 PM</option>
                                <option value="03:30 PM">03:30 PM</option>
                                <option value="04:00 PM">04:00 PM</option>
                                <option value="04:30 PM">04:30 PM</option>
                                <option value="05:00 PM">05:00 PM</option>
                                <option value="05:30 PM">05:30 PM</option>
                                <option value="06:00 PM">06:00 PM</option>
                            </select>
                        </div>
                    </div>

                    <!-- Optional Branch Selector (if multiple branches and user permitted) -->
                    @if(Auth::user()->isAdmin() && count($branches) > 1)
                        <div>
                            <label for="new_branch_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Center Branch
                            </label>
                            <select 
                                name="branch_id" 
                                id="new_branch_id" 
                                class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            >
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ $activeBranchId == $b->id ? 'selected' : '' }}>
                                        {{ $b->branch_name ?? $b->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="branch_id" value="{{ $activeBranchId }}">
                    @endif

                    <!-- Notes -->
                    <div>
                        <label for="new_notes" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Notes / Customer Requests (Optional)
                        </label>
                        <textarea 
                            name="notes" 
                            id="new_notes" 
                            x-model="newNotes"
                            rows="2"
                            placeholder="e.g. Customer requests brake inspection along with full service"
                            class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="showNewModal = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition min-h-[40px]"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-bold rounded-lg shadow-xs transition min-h-[44px]"
                        >
                            Confirm & Schedule Appointment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: APPOINTMENT DETAILS & CONVERT TO JOB CARD -->
    <div 
        x-show="showDetailsModal && selectedAppointment" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" @click="showDetailsModal = false"></div>

            <div class="relative bg-white rounded-2xl w-full max-w-[calc(100vw-2rem)] sm:max-w-lg p-4 sm:p-6 shadow-xl border border-gray-200 space-y-4 max-h-[88vh] overflow-y-auto">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <span class="text-xs text-gray-400 font-mono" x-text="selectedAppointment ? selectedAppointment.appointment_number : ''"></span>
                        <h3 class="text-base font-bold text-gray-900 mt-0.5" x-text="selectedAppointment ? selectedAppointment.vehicle_number : ''"></h3>
                    </div>
                    <button type="button" @click="showDetailsModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Info Cards -->
                <template x-if="selectedAppointment">
                    <div class="space-y-3 text-xs sm:text-sm">
                        <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50 border border-gray-200 rounded-xl">
                            <div>
                                <span class="text-gray-400 block text-[11px] uppercase font-bold">Scheduled Time</span>
                                <span class="font-bold text-gray-900" x-text="selectedAppointment.time + ' on ' + selectedAppointment.date"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[11px] uppercase font-bold">Status</span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase inline-block mt-0.5" :class="getStatusClasses(selectedAppointment.status)" x-text="selectedAppointment.status"></span>
                            </div>
                        </div>

                        <div class="p-3 bg-white border border-gray-200 rounded-xl space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Customer:</span>
                                <span class="font-bold text-gray-900" x-text="selectedAppointment.customer_name"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Mobile Phone:</span>
                                <span class="font-mono text-gray-900" x-text="selectedAppointment.mobile_number"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Vehicle Type:</span>
                                <span class="font-semibold text-gray-900" x-text="selectedAppointment.vehicle_type_name"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Requested Service:</span>
                                <span class="font-semibold text-gray-900" x-text="selectedAppointment.service_name"></span>
                            </div>
                            <template x-if="selectedAppointment.notes">
                                <div class="pt-2 border-t border-gray-100">
                                    <span class="text-gray-400 block text-[11px] uppercase font-bold">Notes</span>
                                    <p class="text-gray-700 italic mt-0.5" x-text="selectedAppointment.notes"></p>
                                </div>
                            </template>
                        </div>

                        <!-- Linked Job Card notice -->
                        <template x-if="selectedAppointment.job_card_id">
                            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-900 flex items-center justify-between">
                                <div>
                                    <span class="font-bold block">Linked Job Card</span>
                                    <span class="text-xs" x-text="'Job #' + selectedAppointment.job_card_number"></span>
                                </div>
                                <a 
                                    :href="selectedAppointment.job_card_url" 
                                    class="px-3 py-1.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-bold rounded-lg shadow-xs transition"
                                >
                                    Open Job Card
                                </a>
                            </div>
                        </template>

                        <!-- Actions footer -->
                        <div class="pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-2">
                            <button 
                                type="button" 
                                @click="showDetailsModal = false"
                                class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition"
                            >
                                Close
                            </button>

                            <div class="flex items-center space-x-2">
                                <!-- Customer Arrived -->
                                <template x-if="['scheduled', 'confirmed'].includes(selectedAppointment.status)">
                                    <form :action="'{{ url('/appointments') }}/' + selectedAppointment.id + '/arrive'" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button 
                                            type="submit" 
                                            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-lg shadow-xs transition cursor-pointer"
                                        >
                                            Customer Arrived
                                        </button>
                                    </form>
                                </template>

                                <!-- Convert to Job Card -->
                                <template x-if="!selectedAppointment.job_card_id && selectedAppointment.status !== 'cancelled'">
                                    <form :action="'{{ url('/appointments') }}/' + selectedAppointment.id + '/create-job-card'" method="POST">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-bold rounded-lg shadow-xs transition cursor-pointer"
                                        >
                                            Create Job Card
                                        </button>
                                    </form>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection
