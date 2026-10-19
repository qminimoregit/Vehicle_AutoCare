@props(['for'])

@error($for)
    <p {{ $attributes->merge(['class' => 'mt-1 text-xs text-rose-600 font-medium']) }}>
        {{ $message }}
    </p>
@enderror
