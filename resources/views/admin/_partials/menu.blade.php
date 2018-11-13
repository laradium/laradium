<!--- Sidemenu -->
<div id="sidebar-menu">
    <ul>
        <li class="text-muted menu-title">Navigation</li>
        @foreach(menu()->get('admin_menu')->items as $item)
            @if (laradium()->hasPermissionTo(auth()->user(), $item->resource))
                <li>
                    <a href="{{ url($item->url) }}"
                       class="{{ str_contains(request()->getRequestUri(), $item->url) ? 'active' : '' }}">
                        <i class="{{ $item->icon ?? 'mdi mdi-view-dashboard' }}"></i>
                        <span>{{ $item->name }}</span>
                    </a>
                </li>
            @endif
        @endforeach

        @if ($belongsTo = laradium()->belongsTo())
            <li class="text-muted menu-title">{{ $belongsTo->getName() }}</li>

            @foreach ($belongsTo->getItems() as $item)
                <li>
                    <a href="#" class="{{ $item->id === $belongsTo->getCurrent() ? 'active' : '' }}">
                        <span>{{ $item->name }}</span>
                    </a>
                </li>
            @endforeach
        @endif
    </ul>
</div>
<!-- Sidebar -->
<div class="clearfix"></div>