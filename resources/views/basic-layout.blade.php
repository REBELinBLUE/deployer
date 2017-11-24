<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="UTF-8" />
        <title>{{ trans('app.name') }}</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
        <link rel="author" href="humans.txt" />

        <link href="{{ mix('/css/vendor.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ mix('/css/app.css') }}" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="{{ mix('/js/ie.js') }}"></script>
        <![endif]-->
    </head>
    <body class="login-page">

        @yield('content')

        <script src="{{ mix('/js/manifest.js') }}"></script>
        <script src="{{ mix('/js/vendor.js') }}"></script>
        <script src="{{ mix('/js/app.js') }}"></script>
    </body>
</html>
