@props([
    'headers' => [],
])

<div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
    <div class="responsive-table-wrapper">
        <table class="min-w-full divide-y divide-gray-200">
            @if(!empty($headers) || isset($thead))
                <thead class="bg-gray-50/80">
                    @if(isset($thead))
                        {{ $thead }}
                    @else
                        <tr>
                            @foreach($headers as $header)
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider {{ $loop->last ? 'text-right' : '' }}">
                                    {{ $header }}
                                </th>
                            @endforeach
                        </tr>
                    @endif
                </thead>
            @endif
            <tbody class="divide-y divide-gray-200 bg-white">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @if(isset($pagination))
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
            {{ $pagination }}
        </div>
    @endif
</div>
