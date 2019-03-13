<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        @if (in_array('laradium', request()->route()->middleware()))
            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Admin</a></li>
        @endif
        @if ($prefix = $resource->getPrefix())
            <li class="breadcrumb-item"><a href="{{ url($prefix) }}">{{ ucfirst($prefix) }}</a></li>
        @endif
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