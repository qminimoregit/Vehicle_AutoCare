@props(['paginator'])

@if($paginator->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50 flex items-center justify-between">
        {{ $paginator->links() }}
    </div>
@endif
