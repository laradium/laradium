<!--- Sidemenu -->
<div id="sidebar-menu">
    <ul>
        <li class="text-muted menu-title">Navigation</li>
        @foreach(config('aven.resources') as $resource)
            @php
                $resourceName = str_replace('_', '-', (new $resource)->getResourceName());
            @endphp
            <li>
                <a href="{{ url('admin/' . $resourceName ) }}" class="waves-effect"><i
                            class="mdi mdi-view-dashboard"></i> <span> {{ ucfirst(str_replace('-', ' ', $resourceName)) }} </span>
                </a>
            </li>
    @endforeach


</div>
<!-- Sidebar -->
<div class="clearfix"></div>