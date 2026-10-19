@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition.duration.300ms class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 shadow-sm flex items-start justify-between">
        <div class="flex items-center space-x-3">
            <svg class="h-5 w-5 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
        <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700 transition">
            <span class="sr-only">Close</span>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif

@if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition.duration.300ms class="mb-5 rounded-lg border border-rose-200 bg-rose-50 p-4 text-rose-800 shadow-sm flex items-start justify-between">
        <div class="flex items-center space-x-3">
            <svg class="h-5 w-5 text-rose-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
        <button type="button" @click="show = false" class="text-rose-500 hover:text-rose-700 transition">
            <span class="sr-only">Close</span>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif

@if($errors->any())
    <div x-data="{ show: true }" x-show="show" class="mb-5 rounded-lg border border-rose-200 bg-rose-50 p-4 text-rose-800 shadow-sm">
        <div class="flex items-start space-x-3">
            <svg class="h-5 w-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="flex-1">
                <h4 class="text-sm font-semibold text-rose-900">Please correct the following errors:</h4>
                <ul class="mt-1 list-disc list-inside text-sm text-rose-700 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" @click="show = false" class="text-rose-500 hover:text-rose-700 transition">
                <span class="sr-only">Close</span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
@endif
