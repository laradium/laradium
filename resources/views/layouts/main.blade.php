<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }} {{ isset($title) ? '- ' . $title :'' }}</title>

    @if(setting()->get('design.admin_theme_favicon'))
        <link rel="shortcut icon" href="{{ setting()->get('design.admin_theme_favicon') }}">
    @endif

    <!-- Styles -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css"
          integrity="sha384-Smlep5jCw/wG7hdkwQ/Z5nLIefveQRIY9nfy6xoR1uRYBtpZgI6339F5dgvm/e9B" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css"
          rel="stylesheet"/>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <link href="{{ asset('/laradium/admin/assets/plugins/switchery/switchery.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('/laradium/admin/assets/plugins/mjolnic-bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}"
          rel="stylesheet">

    <link href="{{ asset('/laradium/admin/assets/css/icons.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('/laradium/admin/assets/css/style.css') }}" rel="stylesheet" type="text/css"/>

    <script src="{{ asset('/laradium/admin/assets/js/modernizr.min.js') }}"></script>
    <link rel="stylesheet" href="{{ versionedAsset('laradium/assets/css/laradium.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            border-top: 3px solid {{setting()->get('design.admin_theme_color', '#71b6f9')}}    !important;
        }

        .topbar .topbar-left {
            border-top: 3px solid {{setting()->get('design.admin_theme_color', '#71b6f9')}}    !important;
        }

        .user-box ul li a:hover {
            color: {{setting()->get('design.admin_theme_color', '#71b6f9')}};
        }

        .text-custom {
            color: {{setting()->get('design.admin_theme_color', '#71b6f9')}}    !important;
        }

        #sidebar-menu > ul > li > a.active {
            border-left: 3px solid{{setting()->get('design.admin_theme_color', '#71b6f9')}};
            color: {{setting()->get('design.admin_theme_color', '#71b6f9')}}    !important;
        }

        #sidebar-menu > ul > li > a:hover {
            color: {{setting()->get('design.admin_theme_color', '#71b6f9')}}    !important;
        }

        a:hover {
            color: {{setting()->get('design.admin_theme_color', '#71b6f9')}};
        }

        a {
            color: {{setting()->get('design.admin_theme_color', '#71b6f9')}};
        }

    </style>

    @include('laradium::admin._partials.variables')
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
                    <img src="{!! setting()->get('design.admin_theme_logo') !!}" alt="Laradium">
                @else
                    Laradium
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
                        <h4 class="page-title">{{ $title ?? 'Laradium' }}</h4>
                    </li>
                </ul>

                @if(isset($resource) && $resource->hasAction('create') && laradium()->hasPermissionTo(auth()->user(), $resource, 'create'))
                    <nav class="navbar-custom">

                        <ul class="list-unstyled topbar-right-menu float-right mb-0">

                            <li>
                                <a href="/admin/{{ $resource->getBaseResource()->getSlug() }}/create"
                                   class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> Create
                                </a>
                            </li>

                        </ul>
                    </nav>
                @endif
            </div><!-- end container -->
        </div><!-- end navbar -->
    </div>
    <!-- Top Bar End -->

    <!-- ========== Left Sidebar Start ========== -->
    <div class="left side-menu">
        <div class="sidebar-inner slimscrollleft">

            <!-- User -->
            <div class="user-box">
                <h5>{{ auth()->user()->name }}</h5>
                <ul class="list-inline">
                    <form id="logout-form" action="/admin/logout" method="POST"
                          style="display: none;">{{ csrf_field() }}</form>
                    <li class="list-inline-item">
                        <a href="javascript:;"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="text-custom"
                        >
                            <i class="mdi mdi-power"></i>
                        </a>
                    </li>
                </ul>
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
            <div class="container-fluid" id="app">
                @yield('content')
            </div> <!-- container -->
        </div> <!-- content -->

        <footer class="footer text-right">© Laradium. <a href="https://netcore.agency">netcore.agency</a></footer>
    </div>
    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->

</div>
<!-- END wrapper -->
@yield('crud-url')
<script src="{{ versionedAsset('laradium/assets/js/laradium.js') }}"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js"
        integrity="sha384-o+RDsa0aLu++PJvFqy8fFScvbHFLtbvScb8AjopnFD+iEQ7wo/CG0xlczd+2O/em"
        crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

<script src="{{ asset('/laradium/admin/assets/js/detect.js') }}"></script>
<script src="{{ asset('/laradium/admin/assets/js/fastclick.js') }}"></script>
<script src="{{ asset('/laradium/admin/assets/js/jquery.blockUI.js') }}"></script>
<script src="{{ asset('/laradium/admin/assets/js/waves.js') }}"></script>
<script src="{{ asset('/laradium/admin/assets/js/jquery.nicescroll.js') }}"></script>
<script src="{{ asset('/laradium/admin/assets/js/jquery.slimscroll.js') }}"></script>
<script src="{{ asset('/laradium/admin/assets/js/jquery.scrollTo.min.js') }}"></script>
<script src="{{ asset('/laradium/admin/assets/plugins/switchery/switchery.min.js') }}"></script>
<script src="{{ asset('/laradium/admin/assets/plugins/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>

<!-- App js -->
<script src="{{ asset('/laradium/admin/assets/js/jquery.core.js') }}"></script>
<script src="{{ asset('/laradium/admin/assets/js/jquery.app.js') }}"></script>

<!-- Laradium js -->
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>

@stack('scripts')

</body>
</html>
