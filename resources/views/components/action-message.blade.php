@props(['on' => null])

@if($on)
    <div x-data="{ shown: false, timeout: null }"
         x-init="
             window.addEventListener('{{ $on }}', () => {
                 clearTimeout(timeout);
                 shown = true;
                 timeout = setTimeout(() => { shown = false }, 2000);
             })
         "
         x-show="shown"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;"
         {{ $attributes->merge(['class' => 'text-sm text-gray-600']) }}>
        {{ $slot->isEmpty() ? __('Saved.') : $slot }}
    </div>
@else
    <div {{ $attributes->merge(['class' => 'text-sm text-gray-600']) }}>
        {{ $slot->isEmpty() ? __('Saved.') : $slot }}
    </div>
@endif
