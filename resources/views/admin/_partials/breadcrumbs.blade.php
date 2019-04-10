<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        @isset($breadcrumbs)
            @foreach ($breadcrumbs as $breadcrumb)
                <li class="breadcrumb-item{{ $loop->last ? ' active' : '' }}" aria-current="page">
                    @if (!$loop->last)
                        <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a>
                    @else
                        {{ $breadcrumb['name'] }}
                    @endif
                </li>
            @endforeach
        @endisset
    </ol>
</nav>