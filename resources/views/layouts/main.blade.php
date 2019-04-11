<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ setting()->get('design.project_title', 'Laradium') }} {{ isset($title) ? '- ' . $title :'' }}</title>

    @if(setting()->get('design.admin_theme_favicon'))
        <link rel="shortcut icon" href="{{ setting()->get('design.admin_theme_favicon') }}">
    @endif

<!-- Styles -->

    {!! $layout->assetManager()->css()->bundle()->custom([
        asset('/laradium/admin/assets/css/icons.css'),
        asset('/laradium/admin/assets/css/style.css')
    ])->render() !!}

    @stack('styles')

    <style>
        .js-tab:not(.active) {
            display: none;
        }

        /*
        theme styles
        @TODO: šo varētu uz atsevišķu view iznest ko pieprasot caur linku atgrieztu atpakaļ kā css
        */

        .navbar-default {
            border-top: 3px solid {{setting()->get('design.admin_theme_color', '#71b6f9')}}           !important;
        }

        .topbar .topbar-left {
            border-top: 3px solid {{setting()->get('design.admin_theme_color', '#71b6f9')}}           !important;
        }

        .user-box ul li a:hover {
            color: {{setting()->get('design.admin_theme_color', '#71b6f9')}};
        }

        .text-custom {
            color: {{setting()->get('design.admin_theme_color', '#71b6f9')}}           !important;
        }

        #sidebar-menu > ul > li > a.active {
            border-left: 3px solid{{setting()->get('design.admin_theme_color', '#71b6f9')}};
            color: {{setting()->get('design.admin_theme_color', '#71b6f9')}}           !important;
        }

        #sidebar-menu > ul > li > a:hover {
            color: {{setting()->get('design.admin_theme_color', '#71b6f9')}}           !important;
        }

        a:hover {
            color: {{setting()->get('design.admin_theme_color', '#71b6f9')}};
        }

        a {
            color: {{setting()->get('design.admin_theme_color', '#71b6f9')}};
        }

        .btn-primary {
            background: {{setting()->get('design.admin_theme_color', '#71b6f9')}}   !important;
            border-color: {{setting()->get('design.admin_theme_color', '#71b6f9')}}   !important;
        }

        .page-item.active .page-link {
            background: {{setting()->get('design.admin_theme_color', '#71b6f9')}}   !important;
            border-color: {{setting()->get('design.admin_theme_color', '#71b6f9')}}   !important;
        }
    </style>

</head>
<body>
<!-- Begin page -->
<div id="wrapper">

    <!-- Top Bar Start -->
    <div class="topbar">

        <!-- LOGO -->
        <div class="topbar-left">
            <a href="{{ url('/') }}" class="logo">
                @if(setting()->get('design.admin_theme_logo'))
                    <img src="{!! setting()->get('design.admin_theme_logo') !!}"
                         alt="{{ setting()->get('design.project_title', 'Laradium') }}">
                @else
                    {{ setting()->get('design.project_title', 'Laradium') }}
                @endif
            </a>
        </div>

        <!-- Button mobile view to collapse sidebar menu -->
        <div class="navbar navbar-default" role="navigation">
            <div class="container-fluid">
                <!-- Page title -->
                <ul class="nav navbar-nav list-inline navbar-left">
                    <li class="list-inline-item">
                        <button class="button-menu-mobile open-left">
                            <i class="mdi mdi-menu"></i>
                        </button>
                    </li>
                    <li class="list-inline-item">
                        <h4 class="page-title">{{ $title ?? setting()->get('design.project_title', 'Laradium') }}</h4>
                    </li>
                </ul>

                @if(isset($resource))
                    <nav class="navbar-custom d-flex align-items-center justify-content-center margin-elements">
                        @if($resource->hasAction('create') && $resource->hasPermission('create'))
                            <a href="/admin/{{ $resource->getBaseResource()->getSlug() }}/create"
                               class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Create
                            </a>
                        @endif

                        @include('laradium::admin.resource._partials.import_export')
                    </nav>
                @endif
            </div><!-- end container -->
        </div><!-- end navbar -->
    </div>
    <!-- Top Bar End -->
    <div id="crud-form">
        <!-- ========== Left Sidebar Start ========== -->
        <div class="left side-menu">
            <div class="sidebar-inner slimscrollleft">

                <!-- User -->
                <div class="user-box">
                    @includeIf(config('laradium.component_views.user-box-top', 'admin._partials.user-box-top'))
                    <form id="logout-form" action="/admin/logout" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                    <h5>
                        {{ auth()->user()->full_name ?? auth()->user()->name }}
                        <a href="javascript:;"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="text-custom"
                        >
                            <i class="mdi mdi-power"></i>
                        </a>
                    </h5>
                    @includeIf(config('laradium.component_views.user-box-bottom', 'admin._partials.user-box-bottom'))
                </div>
                <!-- End User -->

                @include('laradium::admin._partials.menu')
            </div>
        </div>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="content-page">
            <!-- Start content -->
            <div class="content">
                <div class="container-fluid">
                    @yield('content')
                </div> <!-- container -->
            </div> <!-- content -->

            <footer class="footer text-right">
                © {{ setting()->get('design.project_title', 'Laradium') }}. <a href="https://netcore.agency">netcore.agency</a>
            </footer>
        </div>
        <media-manager></media-manager>
    </div>
    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->

</div>
<!-- END wrapper -->
@yield('crud-url')

@isset($jsBeforeSource)
    @foreach($jsBeforeSource as $asset)
        <script src="{{ $asset }}"></script>
    @endforeach
@endisset

{!! $layout->assetManager()->js()->bundle()->render() !!}

@stack('scripts')

</body>
</html>
