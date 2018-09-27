<!--- Sidemenu -->
<div id="sidebar-menu">
    <ul>
        <li class="text-muted menu-title">Navigation</li>
        @foreach(menu()->get('admin_menu')->items as $item)
            @if (!method_exists(auth()->user(), 'hasPermission') || (method_exists(auth()->user(), 'hasPermission') && auth()->user()->hasPermission(request(), $item->resource)))
            <li>
                <a href="{{ url($item->url) }}" class="{{ str_contains(request()->getRequestUri(), $item->url) ? 'active' : '' }}">
                    <i class="{{ $item->icon ?? 'mdi mdi-view-dashboard' }}"></i>
                    <span> {{ $item->name }} </span>
                </a>
            </li>
            @endif
    @endforeach
    </ul>
</div>
<!-- Sidebar -->
<div class="clearfix"></div>