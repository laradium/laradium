<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
    <meta name="author" content="Coderthemes">

    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <title>{{ config('app.name') }} - Login</title>

    <!-- App css -->
    <link href="/laradium/admin/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/laradium/admin/assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="/laradium/admin/assets/css/style.css" rel="stylesheet" type="text/css" />
</head>

<body>

<div class="account-pages"></div>
<div class="clearfix"></div>
<div class="wrapper-page">
    <div class="text-center">
        <a href="{{ url('/') }}" class="logo">
            <span>Lara<span>dium</span></span>
        </a>
    </div>
    <div class="m-t-40 card-box">
        <div class="text-center">
            <h4 class="text-uppercase font-bold m-b-0">Sign In</h4>
        </div>
        <div class="p-20">
            @include('laradium::admin._partials.messages')

            <form class="form-horizontal m-t-20" action="/admin/login" method="post">
                {{ csrf_field() }}
                <div class="form-group">
                    <div class="col-xs-12">
                        <input class="form-control" type="text" required="" name="email" placeholder="Email">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12">
                        <input class="form-control" type="password" name="password" required="" placeholder="Password">
                    </div>
                </div>

                <div class="form-group ">
                    <div class="col-xs-12">
                        <div class="checkbox checkbox-custom">
                            <input id="checkbox-signup" name="remember" type="checkbox">
                            <label for="checkbox-signup">
                                Remember me
                            </label>
                        </div>

                    </div>
                </div>

                <div class="form-group text-center m-t-30">
                    <div class="col-xs-12">
                        <button class="btn btn-custom btn-bordred btn-block waves-effect waves-light" type="submit">Log In</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
    <!-- end card-box-->

</div>

</body>
</html>