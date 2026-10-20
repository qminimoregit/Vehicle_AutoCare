@props(['type' => 'button', 'href' => null])

@if($href)
    <a 
        href="{{ $href }}" 
        {{ $attributes->merge(['class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-sm rounded-lg border border-gray-300 shadow-xs focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition duration-150']) }}
    >
        {{ $slot }}
    </a>
@else
    <button 
        type="{{ $type }}" 
        {{ $attributes->merge(['class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-sm rounded-lg border border-gray-300 shadow-xs focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition duration-150']) }}
    >
        {{ $slot }}
    </button>
@endif
