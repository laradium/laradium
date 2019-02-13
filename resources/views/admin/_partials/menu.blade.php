<!--- Sidemenu -->
@php($uriExploded = explode('/', trim(request()->getRequestUri(), '/')))
<input type="hidden" value="{{ $items }}" name="vue-menu">
<div id="vue-menu">
    @if(isset($form) && get_class($form->getModel()) === config('laradium.menu_class', '\Laradium\Laradium\Models\Menu') && $form->getModel()->key == 'admin_menu')
        <vue-menu :active="'{{ $uriExploded[1] ?? 'none' }}'"></vue-menu>
    @else
        <vue-menu :active="'{{ $uriExploded[1] ?? 'none' }}'">
        </vue-menu>
    @endif
</div>
<!-- Sidebar -->
<div class="clearfix"></div>