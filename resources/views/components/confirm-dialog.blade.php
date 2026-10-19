@props([
    'name',
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to proceed?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'action' => '',
    'method' => 'POST',
    'confirmType' => 'primary', // primary, danger, warning
])

@php
$buttonClasses = match($confirmType) {
    'danger' => 'bg-rose-600 hover:bg-rose-700 text-white focus:ring-rose-500',
    'warning' => 'bg-amber-600 hover:bg-amber-700 text-white focus:ring-amber-500',
    default => 'bg-[#0B1849] hover:bg-[#15286e] text-white focus:ring-[#0B1849]',
};
@endphp

<x-modal :name="$name" maxWidth="md" :title="$title">
    <p class="text-sm text-gray-600 leading-relaxed">
        {{ $message }}
    </p>

    <x-slot:footer>
        <button 
            type="button" 
            x-on:click="$dispatch('close')" 
            class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
        >
            {{ $cancelText }}
        </button>

        <form method="POST" action="{{ $action }}">
            @csrf
            @if(in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
                @method($method)
            @endif
            <button 
                type="submit" 
                class="px-4 py-2 text-sm font-semibold rounded-lg shadow-xs transition {{ $buttonClasses }}"
            >
                {{ $confirmText }}
            </button>
        </form>
    </x-slot:footer>
</x-modal>
