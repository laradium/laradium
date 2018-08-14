<!--- Sidemenu -->
<div id="sidebar-menu">
    <ul>
        <li class="text-muted menu-title">Navigation</li>
        @foreach(config('aven.resources') as $resource)
            @php
                $resourceName = str_replace('Resource', '', array_last(explode('\\', $resource)));
            @endphp
            <li>
                <a href="{{ url('admin/' . strtolower(str_plural($resourceName))) }}" class="waves-effect"><i
                            class="mdi mdi-view-dashboard"></i> <span> {{ str_plural($resourceName) }} </span>
                </a>
            </li>
    @endforeach


</div>
<!-- Sidebar -->
<div class="clearfix"></div>