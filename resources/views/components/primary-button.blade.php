@props(['type' => 'submit'])

<button 
    type="{{ $type }}" 
    {{ $attributes->merge(['class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg shadow-xs focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1849] transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed']) }}
>
    {{ $slot }}
</button>
