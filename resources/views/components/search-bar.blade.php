@props([
    'action' => '',
    'method' => 'GET',
    'search' => '',
    'placeholder' => 'Search records...',
    'resetUrl' => null,
])

<div class="mb-6 bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
    <form method="{{ $method }}" action="{{ $action }}" class="flex flex-col sm:flex-row gap-3">
        <!-- Search Input -->
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input 
                type="text" 
                name="search" 
                value="{{ $search }}" 
                placeholder="{{ $placeholder }}" 
                class="block w-full pl-9 pr-3 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] placeholder:text-gray-400"
            />
        </div>

        {{ $slot }}

        <!-- Action Buttons -->
        <div class="flex items-center space-x-2">
            <button 
                type="submit" 
                class="px-5 py-2.5 text-sm font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg transition shadow-xs cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1849]"
            >
                Search
            </button>
            @if($resetUrl && ($search || request()->has('status') || request()->has('vehicle_type_id')))
                <a 
                    href="{{ $resetUrl }}" 
                    class="px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-lg transition text-center"
                >
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>
