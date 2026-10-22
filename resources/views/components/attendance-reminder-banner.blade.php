@auth
@if((Auth::user()->isAdmin() || Auth::user()->hasPermission('mark_attendance')) && ! request()->routeIs('payroll.attendance.*'))
<div 
    x-data="{
        show: false,
        message: '',
        attendanceUrl: '{{ route('payroll.attendance.index') }}',
        dismissed: false,
        timer: null,
        loading: false,

        init() {
            this.checkStatus();
            // Lightweight 60-second periodic check
            this.timer = setInterval(() => {
                this.checkStatus();
            }, 60000);
        },

        async checkStatus() {
            if (this.dismissed) return;
            try {
                const response = await fetch('{{ route('payroll.attendance.reminder.status') }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (! response.ok) return;
                const data = await response.json();
                if (data.show_reminder) {
                    this.show = true;
                    this.message = data.message;
                    if (data.attendance_url) {
                        this.attendanceUrl = data.attendance_url;
                    }
                } else {
                    this.show = false;
                }
            } catch (err) {
                // Silently handle any network glitches
            }
        },

        async dismissReminder() {
            this.show = false;
            this.dismissed = true;
            try {
                await fetch('{{ route('payroll.attendance.reminder.dismiss') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            } catch (err) {
                // Ignore dismissal network glitch
            }
        }
    }"
    x-show="show"
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 -translate-y-2 scale-98"
    class="mb-6 rounded-xl bg-[#0B1849] border border-blue-900/60 shadow-md text-white p-3.5 sm:p-4 relative"
    role="alert"
    aria-live="polite"
>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
        <!-- Left: Orange Warning Icon & Message -->
        <div class="flex items-start sm:items-center space-x-3 min-w-0">
            <!-- Orange Warning Icon -->
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center shrink-0 shadow-inner">
                <svg class="w-5 h-5 sm:w-5.5 sm:h-5.5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <!-- Text Content -->
            <div class="min-w-0 flex-1">
                <div class="flex items-center space-x-2">
                    <h4 class="text-xs sm:text-sm font-bold tracking-wide uppercase text-white">Attendance Reminder</h4>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">After 3:00 PM</span>
                </div>
                <p class="text-xs sm:text-sm text-slate-200 mt-0.5 font-medium" x-text="message">
                    Employees have not been marked today.
                </p>
            </div>
        </div>

        <!-- Right: Action Buttons (Responsive on Mobile/Tablet/Desktop) -->
        <div class="flex items-center space-x-2 shrink-0 self-end sm:self-center pl-12 sm:pl-0">
            <!-- Mark Attendance Button -->
            <a 
                :href="attendanceUrl" 
                class="inline-flex items-center justify-center space-x-1.5 px-3 sm:px-3.5 py-1.5 rounded-lg text-xs font-bold bg-amber-500 hover:bg-amber-400 active:bg-amber-600 text-gray-950 transition shadow-xs focus:outline-none focus:ring-2 focus:ring-amber-300 cursor-pointer"
                title="Open Employee Attendance page"
            >
                <svg class="w-3.5 h-3.5 text-gray-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Mark Attendance</span>
            </a>

            <!-- Dismiss Button -->
            <button 
                type="button" 
                @click="dismissReminder()" 
                class="inline-flex items-center justify-center px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-medium text-slate-300 hover:text-white hover:bg-white/10 transition focus:outline-none cursor-pointer"
                title="Dismiss reminder for today"
            >
                <span>Dismiss</span>
            </button>
        </div>
    </div>
</div>
@endif
@endauth
