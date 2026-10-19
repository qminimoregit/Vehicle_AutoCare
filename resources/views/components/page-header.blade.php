@props(['title', 'description' => null])

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ $title }}</h1>
        @if($description)
            <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
        @endif
    </div>
    @if(isset($actions))
        <div class="flex items-center space-x-3">
            {{ $actions }}
        </div>
    @endif
</div>
