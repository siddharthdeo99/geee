@props(['value', 'type_id'])

<div class="price bg-price-gradient block p-1 pr-[calc(0.5rem+1em)] text-black relative border border-r-0 border-black  overflow-hidden whitespace-nowrap truncate leading-[1.1rem] text-sm" itemprop="price" content="1">
    @if($type_id == 1)
        {{ $value }}
    @elseif($type_id == 2)
        Free
    @elseif($type_id == 3)
        Please Contact
    @elseif($type_id == 4)
        Swap/Trade
    @else
        {{ $value ?? $slot }}
    @endif
</div>

