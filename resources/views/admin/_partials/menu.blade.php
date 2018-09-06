<!--- Sidemenu -->
<div id="sidebar-menu">
    <ul>
        <li class="text-muted menu-title">Navigation</li>
        @foreach(menu()->get('admin_menu')->items as $item)
            <li>
                <a href="{{ url($item->url ) }}" class="{{ str_contains(request()->getRequestUri(), $item->url) ? 'active' : '' }}">
                    <i class="{{ $item->icon ?? 'mdi mdi-view-dashboard' }}"></i>
                    <span> {{ $item->name }} </span>
                </a>
            </li>
    @endforeach
    </ul>
</div>
<!-- Sidebar -->
<div class="clearfix"></div>