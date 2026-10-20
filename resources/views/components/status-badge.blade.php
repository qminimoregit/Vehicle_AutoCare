@props(['status'])

@php
    $normalized = strtolower(trim((string)$status));
    $classes = match($normalized) {
        'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200 ring-1 ring-emerald-600/20',
        'inactive' => 'bg-gray-100 text-gray-600 border-gray-200 ring-1 ring-gray-400/20',
        'admin' => 'bg-indigo-50 text-indigo-700 border-indigo-200 ring-1 ring-indigo-600/20',
        'staff' => 'bg-sky-50 text-sky-700 border-sky-200 ring-1 ring-sky-600/20',
        'coming_soon', 'soon' => 'bg-amber-50 text-amber-700 border-amber-200 ring-1 ring-amber-600/20',
        default => 'bg-gray-100 text-gray-700 border-gray-200',
    };
    $label = match($normalized) {
        'active' => 'Active',
        'inactive' => 'Inactive',
        'admin' => 'Admin',
        'staff' => 'Staff',
        'coming_soon', 'soon' => 'Coming Soon',
        default => ucfirst($status),
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {$classes}"]) }}>
    @if(in_array($normalized, ['active', 'inactive']))
        <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $normalized === 'active' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
    @endif
    {{ $label }}
</span>
