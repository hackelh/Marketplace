@props(['messages' => null])

@if ($messages && (is_array($messages) || $messages instanceof \Illuminate\Support\Collection))
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            @if(!empty(trim($message)))
                <li>{{ $message }}</li>
            @endif
        @endforeach
    </ul>
@elseif(!empty($messages))
    <p {{ $attributes->merge(['class' => 'text-sm text-red-600']) }}>
        {{ $messages }}
    </p>
@endif
