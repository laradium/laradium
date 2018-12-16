<!--- Sidemenu -->
@php($uriExploded = explode('/', trim(request()->getRequestUri(), '/')))
<div id="vue-menu">
    @if(isset($form) && get_class($form->getModel()) === config('laradium.menu_class', '\Laradium\Laradium\Models\Menu') && $form->getModel()->key == 'admin_menu')
        <vue-menu :active="'{{ $uriExploded[1] ?? 'none' }}'"></vue-menu>
    @else
        <div id="sidebar-menu">
            <ul>
                <li class="text-muted menu-title">Navigation</li>
            </ul>
            <ul>
                @foreach(menu()->get('admin_menu')->items->where('parent_id', null) as $item)
                    @if (laradium()->hasPermissionTo(auth()->user(), $item->resource) && laradium()->belongsTo()->hasAccess($item->resource))
                        <li {{ $item->children->count() ? 'class="has_sub"': '' }}>
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
                    @endif
                @endforeach

                @if ($belongsTo = laradium()->belongsTo())
                    <li class="text-muted menu-title">{{ $belongsTo->getName() }}</li>

                    @foreach ($belongsTo->getItems() as $item)
                        <li>
                            <a href="{{ route('admin.change-belongsto', $item->id) }}"
                               class="{{ $item->id == $belongsTo->getCurrent() ? 'active' : '' }}">
                                    <span>
                                        @if ($item->id == $belongsTo->getCurrent())
                                            <i class="fa fa-check"></i>
                                        @endif {{ $item->name }}
                                    </span>
                            </a>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
    @endif
</div>
<!-- Sidebar -->
<div class="clearfix"></div>