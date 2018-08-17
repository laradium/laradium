<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Aven {{ isset($title) ? '- ' . $title :'' }}</title>

    <!-- Styles -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css"
          integrity="sha384-Smlep5jCw/wG7hdkwQ/Z5nLIefveQRIY9nfy6xoR1uRYBtpZgI6339F5dgvm/e9B" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css"
          rel="stylesheet"/>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <link href="/aven/admin/assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
    <link href="/aven/admin/assets/css/icons.css" rel="stylesheet" type="text/css"/>
    <link href="/aven/admin/assets/css/style.css" rel="stylesheet" type="text/css"/>

    <script src="/aven/admin/assets/js/modernizr.min.js"></script>
    <style>
        .js-tab:not(.active) {
            display: none;
        }
    </style>
    <link rel="stylesheet" href="{{ mix('aven/assets/css/aven.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')
</head>
<body class="fixed-left">
<!-- Begin page -->
<div id="wrapper">

    <!-- Top Bar Start -->
    <div class="topbar">

        <!-- LOGO -->
        <div class="topbar-left">
            <a href="{{ url('/') }}" class="logo">
                <img src="/aven/logo.svg" alt="Aven" style="max-width: 90%;">
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
                        <h4 class="page-title">{{ $title ?? 'Aven' }}</h4>
                    </li>
                </ul>
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

            @include('aven::admin._partials.menu')

        </div>

    </div>
    <!-- Left Sidebar End -->


    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="content-page">
        <!-- Start content -->
        <div class="content">
            <br>
            <div class="container-fluid">
                @yield('content')
            </div> <!-- container -->

        </div> <!-- content -->

        <footer class="footer text-right">
            © Aven. <a href="https://netcore.agency">netcore.agency</a>
        </footer>

    </div>


    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->

</div>
<!-- END wrapper -->
@yield('crud-url')
<script src="{{ mix('aven/assets/js/aven.js') }}"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js"
        integrity="sha384-o+RDsa0aLu++PJvFqy8fFScvbHFLtbvScb8AjopnFD+iEQ7wo/CG0xlczd+2O/em"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>


<script src="/aven/admin/assets/js/detect.js"></script>
<script src="/aven/admin/assets/js/fastclick.js"></script>
<script src="/aven/admin/assets/js/jquery.blockUI.js"></script>
<script src="/aven/admin/assets/js/waves.js"></script>
<script src="/aven/admin/assets/js/jquery.nicescroll.js"></script>
<script src="/aven/admin/assets/js/jquery.slimscroll.js"></script>
<script src="/aven/admin/assets/js/jquery.scrollTo.min.js"></script>
<script src="/aven/admin/assets/plugins/switchery/switchery.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<!-- App js -->
<script src="/aven/admin/assets/js/jquery.core.js"></script>
<script src="/aven/admin/assets/js/jquery.app.js"></script>

<!-- Aven js -->

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function () {
        $('textarea.summer-note').summernote({
            height: 150
        });
    });
</script>

@stack('scripts')

</body>
</html>
