<a href="{{ $url }}" class="text-nowrap table-order-button">
    {{ $name }}
    @if($isActive)
        @if($orderDirection === 'asc')
            <i class="fa-solid fa-arrow-down"></i>
        @else
            <i class="fa-solid fa-arrow-up"></i>
        @endif
    @endif
</a>
