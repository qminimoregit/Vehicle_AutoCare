@extends('layouts.app')

@section('title', 'Daily Attendance')

@section('content')
<div 
    class="max-w-4xl mx-auto space-y-5"
    x-data="{
        showConfirmModal: false,
        fullDayCount: 0,
        halfDayCount: 0,
        absentCount: 0,
        calendarOpen: false,

        selectAllPresent() {
            document.querySelectorAll('.employee-row').forEach(row => {
                let p = row.querySelector('.present-cb');
                let h = row.querySelector('.halfday-cb');
                if (p) {
                    p.checked = true;
                    p.dispatchEvent(new Event('change'));
                }
                if (h) {
                    h.checked = false;
                    h.dispatchEvent(new Event('change'));
                }
            });
        },

        deselectAll() {
            document.querySelectorAll('.employee-row').forEach(row => {
                let p = row.querySelector('.present-cb');
                let h = row.querySelector('.halfday-cb');
                if (p) {
                    p.checked = false;
                    p.dispatchEvent(new Event('change'));
                }
                if (h) {
                    h.checked = false;
                    h.dispatchEvent(new Event('change'));
                }
            });
        },

        prepareSave() {
            let full = 0;
            let half = 0;
            let absent = 0;

            document.querySelectorAll('.employee-row').forEach(row => {
                let p = row.querySelector('.present-cb');
                let h = row.querySelector('.halfday-cb');
                let isPresent = p ? p.checked : false;
                let isHalf = h ? h.checked : false;

                if (isPresent && isHalf) {
                    half++;
                } else if (isPresent) {
                    full++;
                } else {
                    absent++;
                }
            });

            this.fullDayCount = full;
            this.halfDayCount = half;
            this.absentCount = absent;
            this.showConfirmModal = true;
        },

        confirmAndSubmit() {
            this.showConfirmModal = false;
            document.getElementById('attendance-form').submit();
        }
    }"
>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">Daily Attendance</h1>
            <p class="text-xs text-gray-500 mt-0.5">Mark daily staff attendance quickly using simple checkboxes.</p>
        </div>

        @php
            $isSaved = $existingRecords->count() > 0;
        @endphp
        <div class="flex items-center gap-2">
            @if($isSaved)
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5"></span>
                    Saved: {{ $summary['full_day'] }} Full &bull; {{ $summary['half_day'] }} Half &bull; {{ $summary['absent'] }} Absent
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-1.5 animate-pulse"></span>
                    Attendance Not Yet Recorded
                </span>
            @endif
        </div>
    </div>

    <!-- Date Navigation Toolbar -->
    <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs flex flex-wrap items-center justify-between gap-3">
        <!-- Quick Date Buttons -->
        <div class="flex items-center gap-1.5">
            <a 
                href="{{ route('payroll.attendance.index', ['date' => $selectedDate->copy()->subDay()->toDateString(), 'cal_month' => $calMonth, 'cal_year' => $calYear]) }}" 
                class="px-3 py-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
            >
                &larr; Previous Day
            </a>

            @if(!$selectedDate->isToday())
                <a 
                    href="{{ route('payroll.attendance.index', ['date' => today()->toDateString(), 'cal_month' => $calMonth, 'cal_year' => $calYear]) }}" 
                    class="px-3 py-1.5 text-xs font-semibold text-[#0B1849] bg-[#EEF1FA] hover:bg-[#dfe4f5] rounded-lg transition"
                >
                    Today
                </a>
            @endif

            <a 
                href="{{ route('payroll.attendance.index', ['date' => $selectedDate->copy()->addDay()->toDateString(), 'cal_month' => $calMonth, 'cal_year' => $calYear]) }}" 
                class="px-3 py-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
            >
                Next Day &rarr;
            </a>
        </div>

        <!-- Date Picker Input -->
        <form method="GET" action="{{ route('payroll.attendance.index') }}" class="flex items-center gap-2">
            <input type="hidden" name="cal_month" value="{{ $calMonth }}">
            <input type="hidden" name="cal_year" value="{{ $calYear }}">

            <label for="date" class="text-xs font-semibold text-gray-600">Select Date:</label>
            <input 
                type="date" 
                name="date" 
                id="date" 
                value="{{ $selectedDate->toDateString() }}" 
                onchange="this.form.submit()" 
                class="py-1.5 px-3 text-xs font-semibold text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white cursor-pointer"
            >
            <span class="text-xs font-medium text-gray-500 hidden md:inline">
                {{ $selectedDate->format('l, d M Y') }}
            </span>
        </form>
    </div>

    @if(!$canEdit)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 flex items-center text-xs text-amber-800">
            <svg class="w-4 h-4 mr-2 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>You are viewing past attendance in read-only mode. Only Administrators can modify historical attendance.</span>
        </div>
    @endif

    <!-- Attendance Form -->
    <form id="attendance-form" method="POST" action="{{ route('payroll.attendance.store') }}">
        @csrf
        <input type="hidden" name="attendance_date" value="{{ $selectedDate->toDateString() }}">

        <!-- Desktop Attendance Table -->
        <div class="hidden md:block bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Roster &bull; {{ $selectedDate->format('d F Y') }}
                    </span>
                    <span class="text-xs text-gray-400">({{ $employees->count() }} employees)</span>
                </div>

                @if($canEdit)
                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            @click="selectAllPresent()" 
                            class="px-2.5 py-1 text-xs font-semibold text-[#0B1849] hover:bg-[#EEF1FA] rounded-md transition cursor-pointer"
                        >
                            Select All Present
                        </button>
                        <span class="text-gray-300">|</span>
                        <button 
                            type="button" 
                            @click="deselectAll()" 
                            class="px-2.5 py-1 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-md transition cursor-pointer"
                        >
                            Clear All
                        </button>
                    </div>
                @endif
            </div>

            <div class="responsive-table-wrapper">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider w-36">
                                Employee ID
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Employee Name
                            </th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-36">
                                Attendance
                            </th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-36">
                                Half Day
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($employees as $emp)
                            @php
                                $rec = $existingRecords->get($emp->id);
                                $isPresent = $rec && in_array($rec->status, ['full_day', 'half_day']);
                                $isHalf = $rec && $rec->status === 'half_day';
                            @endphp
                            <tr 
                                class="employee-row hover:bg-slate-50 transition" 
                                x-data="{
                                    present: {{ $isPresent ? 'true' : 'false' }},
                                    halfDay: {{ $isHalf ? 'true' : 'false' }},
                                    onHalfDayChange() {
                                        if (this.halfDay) {
                                            this.present = true;
                                        }
                                    },
                                    onPresentChange() {
                                        if (!this.present) {
                                            this.halfDay = false;
                                        }
                                    }
                                }"
                            >
                                <td class="px-5 py-3.5 whitespace-nowrap text-xs font-mono font-bold text-gray-500">
                                    {{ $emp->employee_number }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span class="text-sm font-bold text-gray-900 block">{{ $emp->name }}</span>
                                    <span class="text-[11px] text-gray-400 font-medium">{{ $emp->position ?: 'Staff' }}</span>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-center">
                                    @if($canEdit)
                                        <input type="hidden" name="attendances[{{ $emp->id }}][present]" value="0">
                                        <label class="inline-flex items-center justify-center cursor-pointer p-1">
                                            <input 
                                                type="checkbox" 
                                                name="attendances[{{ $emp->id }}][present]" 
                                                value="1" 
                                                x-model="present" 
                                                @change="onPresentChange()"
                                                class="present-cb w-5 h-5 rounded text-[#0B1849] border-gray-300 focus:ring-[#0B1849] cursor-pointer"
                                            >
                                        </label>
                                    @else
                                        <span class="inline-flex items-center justify-center w-5 h-5">
                                            @if($isPresent)
                                                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @else
                                                <span class="text-gray-300">&mdash;</span>
                                            @endif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-center">
                                    @if($canEdit)
                                        <input type="hidden" name="attendances[{{ $emp->id }}][half_day]" value="0">
                                        <label class="inline-flex items-center justify-center cursor-pointer p-1">
                                            <input 
                                                type="checkbox" 
                                                name="attendances[{{ $emp->id }}][half_day]" 
                                                value="1" 
                                                x-model="halfDay" 
                                                @change="onHalfDayChange()"
                                                class="halfday-cb w-5 h-5 rounded text-blue-600 border-gray-300 focus:ring-blue-600 cursor-pointer"
                                            >
                                        </label>
                                    @else
                                        <span class="inline-flex items-center justify-center w-5 h-5">
                                            @if($isHalf)
                                                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @else
                                                <span class="text-gray-300">&mdash;</span>
                                            @endif
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Touch Attendance Cards -->
        <div class="block md:hidden space-y-2.5">
            @if($canEdit)
                <div class="flex items-center justify-between px-1">
                    <button 
                        type="button" 
                        @click="selectAllPresent()" 
                        class="text-xs font-bold text-[#0B1849] py-1"
                    >
                        + Select All Present
                    </button>
                    <button 
                        type="button" 
                        @click="deselectAll()" 
                        class="text-xs font-semibold text-gray-500 py-1"
                    >
                        Clear All
                    </button>
                </div>
            @endif

            @foreach($employees as $emp)
                @php
                    $rec = $existingRecords->get($emp->id);
                    $isPresent = $rec && in_array($rec->status, ['full_day', 'half_day']);
                    $isHalf = $rec && $rec->status === 'half_day';
                    $wage = $emp->getWageForDate($selectedDate);
                @endphp
                <div 
                    class="employee-row bg-white rounded-xl border border-gray-200 p-3.5 shadow-xs flex flex-col gap-3"
                    x-data="{
                        present: {{ $isPresent ? 'true' : 'false' }},
                        halfDay: {{ $isHalf ? 'true' : 'false' }},
                        baseWage: {{ (float)$wage }},
                        onHalfDayChange() {
                            if (this.halfDay) {
                                this.present = true;
                            }
                        },
                        onPresentChange() {
                            if (!this.present) {
                                this.halfDay = false;
                            }
                        },
                        currentWage() {
                            if (!this.present) return '0.00';
                            if (this.halfDay) return (this.baseWage * 0.5).toFixed(2);
                            return this.baseWage.toFixed(2);
                        }
                    }"
                >
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="text-xs text-gray-400 font-mono block">{{ $emp->employee_number }} &bull; {{ $emp->position ?: 'Staff' }}</span>
                            <span class="text-sm font-bold text-gray-900 block">{{ $emp->name }}</span>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[10px] text-gray-400 uppercase tracking-wider block font-semibold">Daily Rate</span>
                            <span class="text-xs font-bold text-gray-700 font-mono">LKR {{ number_format($wage, 2) }}</span>
                        </div>
                    </div>

                    @if($canEdit)
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                            <!-- Dynamic Earnings Preview -->
                            <div class="text-xs">
                                <span class="text-gray-500">Day Pay:</span>
                                <span class="font-bold font-mono text-[#0B1849]" x-text="'LKR ' + currentWage()"></span>
                            </div>

                            <div class="flex items-center gap-2 sm:gap-4">
                                <!-- Attendance Checkbox -->
                                <input type="hidden" name="attendances[{{ $emp->id }}][present]" value="0">
                                <label class="flex items-center gap-1.5 cursor-pointer py-1.5 px-2 rounded-lg hover:bg-gray-50 min-h-[44px]">
                                    <input 
                                        type="checkbox" 
                                        name="attendances[{{ $emp->id }}][present]" 
                                        value="1" 
                                        x-model="present" 
                                        @change="onPresentChange()"
                                        class="present-cb w-5 h-5 rounded text-[#0B1849] border-gray-300 focus:ring-[#0B1849]"
                                    >
                                    <span class="text-xs font-semibold text-gray-800">Present</span>
                                </label>

                                <!-- Half Day Checkbox -->
                                <input type="hidden" name="attendances[{{ $emp->id }}][half_day]" value="0">
                                <label class="flex items-center gap-1.5 cursor-pointer py-1.5 px-2 rounded-lg hover:bg-gray-50 min-h-[44px]">
                                    <input 
                                        type="checkbox" 
                                        name="attendances[{{ $emp->id }}][half_day]" 
                                        value="1" 
                                        x-model="halfDay" 
                                        @change="onHalfDayChange()"
                                        class="halfday-cb w-5 h-5 rounded text-blue-600 border-gray-300 focus:ring-blue-600"
                                    >
                                    <span class="text-xs font-semibold text-gray-800">Half Day</span>
                                </label>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100 text-xs">
                            <div>
                                <span class="text-gray-500">Day Pay:</span>
                                <span class="font-bold font-mono text-gray-900">
                                    LKR {{ number_format($wage * ($isHalf ? 0.5 : ($isPresent ? 1.0 : 0)), 2) }}
                                </span>
                            </div>
                            <div class="font-bold font-mono">
                                @if($isHalf)
                                    <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[11px]">Half Day</span>
                                @elseif($isPresent)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px]">Full Day</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 text-[11px]">Absent</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- SINGLE SAVE ATTENDANCE BUTTON -->
        @if($canEdit)
            <div class="pt-4 flex justify-center sm:justify-end">
                <button 
                    type="button" 
                    @click="prepareSave()"
                    class="w-full sm:w-auto px-8 py-3 bg-[#0B1849] hover:bg-[#15286e] text-white font-bold text-sm rounded-xl shadow-xs transition duration-150 flex items-center justify-center cursor-pointer min-h-[44px]"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Attendance
                </button>
            </div>
        @endif
    </form>

    <!-- SECTION 6: Collapsible Attendance Calendar -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-xs">
        <button 
            type="button" 
            @click="calendarOpen = !calendarOpen"
            class="w-full flex items-center justify-between p-4 text-left font-semibold text-gray-800 hover:bg-slate-50 transition cursor-pointer"
        >
            <span class="flex items-center text-sm font-bold text-gray-900">
                <svg class="w-4 h-4 mr-2 text-[#0B1849]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Attendance Calendar
            </span>
            <span class="flex items-center text-xs text-gray-500 font-medium">
                <span x-text="calendarOpen ? 'Hide Calendar' : 'View Monthly Calendar'"></span>
                <svg :class="calendarOpen ? 'rotate-180' : ''" class="w-4 h-4 ml-1.5 transform transition-transform text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </span>
        </button>

        <div x-show="calendarOpen" x-cloak class="p-4 border-t border-gray-100 space-y-4">
            <!-- Calendar Header with Month Navigation -->
            @php
                $prevCalMonth = $calMonth == 1 ? 12 : $calMonth - 1;
                $prevCalYear = $calMonth == 1 ? $calYear - 1 : $calYear;
                $nextCalMonth = $calMonth == 12 ? 1 : $calMonth + 1;
                $nextCalYear = $calMonth == 12 ? $calYear + 1 : $calYear;
            @endphp
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <a 
                        href="{{ route('payroll.attendance.index', ['date' => $selectedDate->toDateString(), 'cal_month' => $prevCalMonth, 'cal_year' => $prevCalYear]) }}" 
                        class="px-2.5 py-1 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                    >
                        &larr; Prev
                    </a>
                    <span class="text-sm font-bold text-gray-900 px-2 font-mono">
                        {{ $calendarDate->format('F Y') }}
                    </span>
                    <a 
                        href="{{ route('payroll.attendance.index', ['date' => $selectedDate->toDateString(), 'cal_month' => $nextCalMonth, 'cal_year' => $nextCalYear]) }}" 
                        class="px-2.5 py-1 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                    >
                        Next &rarr;
                    </a>
                </div>

                <!-- Legend -->
                <div class="hidden sm:flex items-center gap-3 text-[11px] text-gray-500">
                    <span class="inline-flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-1"></span> All Marked</span>
                    <span class="inline-flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-amber-400 mr-1"></span> Partial</span>
                    <span class="inline-flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 mr-1"></span> Missing</span>
                    <span class="inline-flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-gray-300 mr-1"></span> Future / Off</span>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-1.5 pt-1">
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dow)
                    <div class="text-center text-[10px] font-bold uppercase text-gray-400 py-1">
                        {{ $dow }}
                    </div>
                @endforeach

                @php
                    $firstDayDow = $calendarDate->copy()->startOfMonth()->dayOfWeek;
                @endphp
                @for($i = 0; $i < $firstDayDow; $i++)
                    <div class="h-12 rounded-lg bg-gray-50/50"></div>
                @endfor

                @foreach($calendarDays as $cDay)
                    @php
                        $bgColor = match($cDay['color']) {
                            'green' => 'bg-emerald-50 hover:bg-emerald-100 border-emerald-300 text-emerald-950',
                            'amber' => 'bg-amber-50 hover:bg-amber-100 border-amber-300 text-amber-950',
                            'red' => 'bg-rose-50 hover:bg-rose-100 border-rose-300 text-rose-950',
                            default => 'bg-gray-50 hover:bg-gray-100 border-gray-200 text-gray-500',
                        };
                        $indicatorDot = match($cDay['color']) {
                            'green' => 'bg-emerald-500',
                            'amber' => 'bg-amber-500',
                            'red' => 'bg-rose-500',
                            default => 'bg-gray-300',
                        };
                    @endphp
                    <a 
                        href="{{ route('payroll.attendance.index', ['date' => $cDay['date'], 'cal_month' => $calMonth, 'cal_year' => $calYear]) }}" 
                        class="h-12 sm:h-14 p-1.5 rounded-lg border flex flex-col justify-between transition cursor-pointer {{ $bgColor }} {{ $cDay['is_selected'] ? 'ring-2 ring-[#0B1849] font-bold' : '' }}"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold font-mono">{{ $cDay['day'] }}</span>
                            <span class="w-1.5 h-1.5 rounded-full {{ $indicatorDot }}"></span>
                        </div>
                        <span class="text-[9px] truncate font-medium">
                            {{ $cDay['is_today'] ? 'TODAY' : $cDay['status_text'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Attendance Confirmation Modal -->
    <div 
        x-show="showConfirmModal" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div @click="showConfirmModal = false" class="fixed inset-0 transition-opacity bg-gray-900/50 backdrop-blur-xs"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Confirm Daily Attendance</h3>
                        <p class="text-xs text-gray-500">{{ $selectedDate->format('l, d F Y') }}</p>
                    </div>
                    <button type="button" @click="showConfirmModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="my-4 p-4 bg-gray-50 rounded-xl space-y-2.5 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center text-xs font-bold text-emerald-800">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
                            Full Day
                        </span>
                        <span class="font-mono font-bold text-gray-900" x-text="fullDayCount + ' employee(s)'"></span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="flex items-center text-xs font-bold text-blue-800">
                            <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span>
                            Half Day
                        </span>
                        <span class="font-mono font-bold text-gray-900" x-text="halfDayCount + ' employee(s)'"></span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="flex items-center text-xs font-bold text-rose-800">
                            <span class="w-2 h-2 rounded-full bg-rose-500 mr-2"></span>
                            Absent (Unchecked)
                        </span>
                        <span class="font-mono font-bold text-rose-700" x-text="absentCount + ' employee(s)'"></span>
                    </div>
                </div>

                <p class="text-xs text-gray-600 mb-5 leading-relaxed">
                    Unchecked employees will be recorded as <strong>Absent</strong> for this date.
                    Do you confirm and want to save this attendance?
                </p>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                    <button 
                        type="button" 
                        @click="showConfirmModal = false" 
                        class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                    >
                        Cancel
                    </button>
                    <button 
                        type="button" 
                        @click="confirmAndSubmit()" 
                        class="px-5 py-2 text-xs font-bold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-xs transition"
                    >
                        Confirm & Save Attendance
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
