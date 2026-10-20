@props(['type' => null, 'message' => null])

@php
    $success = ($type === 'success' && $message) ? $message : session('success');
    $error = ($type === 'error' && $message) ? $message : session('error');
    $warning = ($type === 'warning' && $message) ? $message : session('warning');
    $info = ($type === 'info' && $message) ? $message : session('info');
@endphp

@if($success)
    <div 
        x-data="{ show: true }" 
        x-show="show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50/90 p-4 text-emerald-800 shadow-xs flex items-start justify-between"
        role="alert"
    >
        <div class="flex items-center space-x-3 min-w-0">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <p class="text-sm font-semibold text-emerald-900 break-words">{{ $success }}</p>
        </div>
        <button 
            type="button" 
            @click="show = false" 
            class="ml-3 p-1 rounded-lg text-emerald-600 hover:text-emerald-800 hover:bg-emerald-100/60 transition focus:outline-none"
            aria-label="Dismiss notification"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif

@if($warning)
    <div 
        x-data="{ show: true }" 
        x-show="show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="mb-6 rounded-xl border border-amber-200 bg-amber-50/90 p-4 text-amber-900 shadow-xs flex items-start justify-between"
        role="alert"
    >
        <div class="flex items-center space-x-3 min-w-0">
            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <p class="text-sm font-semibold text-amber-950 break-words">{{ $warning }}</p>
        </div>
        <button 
            type="button" 
            @click="show = false" 
            class="ml-3 p-1 rounded-lg text-amber-700 hover:text-amber-900 hover:bg-amber-100/60 transition focus:outline-none"
            aria-label="Dismiss notification"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif

@if($info)
    <div 
        x-data="{ show: true }" 
        x-show="show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="mb-6 rounded-xl border border-blue-200 bg-blue-50/90 p-4 text-blue-900 shadow-xs flex items-start justify-between"
        role="alert"
    >
        <div class="flex items-center space-x-3 min-w-0">
            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-800 flex items-center justify-center shrink-0">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-sm font-semibold text-blue-950 break-words">{{ $info }}</p>
        </div>
        <button 
            type="button" 
            @click="show = false" 
            class="ml-3 p-1 rounded-lg text-blue-700 hover:text-blue-900 hover:bg-blue-100/60 transition focus:outline-none"
            aria-label="Dismiss notification"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif

@if($error)
    <div 
        x-data="{ show: true }" 
        x-show="show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="mb-6 rounded-xl border border-rose-200 bg-rose-50/90 p-4 text-rose-900 shadow-xs flex items-start justify-between"
        role="alert"
    >
        <div class="flex items-center space-x-3 min-w-0">
            <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-800 flex items-center justify-center shrink-0">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <p class="text-sm font-semibold text-rose-950 break-words">{{ $error }}</p>
        </div>
        <button 
            type="button" 
            @click="show = false" 
            class="ml-3 p-1 rounded-lg text-rose-600 hover:text-rose-800 hover:bg-rose-100/60 transition focus:outline-none"
            aria-label="Dismiss notification"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif

@if($errors->any())
    <div 
        x-data="{ show: true }" 
        x-show="show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="mb-6 rounded-xl border border-rose-200 bg-rose-50/90 p-4 text-rose-900 shadow-xs"
        role="alert"
    >
        <div class="flex items-start space-x-3">
            <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-800 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="text-sm font-bold text-rose-950">Please correct the following errors:</h4>
                <ul class="mt-1 list-disc list-inside text-xs text-rose-800 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            <button 
                type="button" 
                @click="show = false" 
                class="ml-3 p-1 rounded-lg text-rose-600 hover:text-rose-800 hover:bg-rose-100/60 transition focus:outline-none"
                aria-label="Dismiss errors"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
@endif
