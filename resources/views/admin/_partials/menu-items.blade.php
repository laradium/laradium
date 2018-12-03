<ul class="list-unstyled">
    @foreach($items as $item)
        <li {{ $item->children ? 'class="has_sub"': '' }}>
            <a href="{{ $item->children->count() ? 'javascript:;' : url($item->url) }}"
               class="{{ str_contains(( $uriExploded[1] ?? 'none'), $item->url) ? 'active' : '' }}">
                <i class="{{ $item->icon ?? 'mdi mdi-view-dashboard' }}"></i>
                <span> {{ $item->name }} </span>
                @if($item->children->count())
                    <div class="pull-right">
                        <span class="fa fa-caret-down"></span>
                    </div>
                @endif
            </a>
            @if($item->children->count())
                {!! menuItems($item->children) !!}
            @endif
        </li>
    @endforeach
</ul>