@props(['title', 'description' => null, 'icon' => null])

<div class="py-12 px-4 text-center">
    <div class="max-w-md mx-auto text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 text-gray-400 mb-3">
            @if($icon)
                {{ $icon }}
            @else
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
            @endif
        </div>
        <h3 class="text-sm font-bold text-gray-900">{{ $title }}</h3>
        @if($description)
            <p class="mt-1 text-xs text-gray-500 leading-relaxed">{{ $description }}</p>
        @endif
        @if(isset($action))
            <div class="mt-4">
                {{ $action }}
            </div>
        @endif
    </div>
</div>
