<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Admin</a></li>
        @if (isset($items))
            @foreach ($items as $item)
                <li class="breadcrumb-item{{ $loop->last ? ' active' : '' }}" aria-current="page">
                    @if (!$loop->last)
                        <a href="{{ $item['url'] }}">{{ $item['name'] }}</a>
                    @else
                        {{ $item['name'] }}
                    @endif
                </li>
            @endforeach
        @endif
    </ol>
</nav>