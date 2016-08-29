<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>{{ Lang::get('app.name') }}</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
        <link rel="author" href="humans.txt" />

        <!-- Style -->
        <link href="{{ webpack('css/vendor.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ webpack('css/app.css') }}" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="{{ webpack('js/ie.js') }}"></script>
        <![endif]-->
    </head>
    <body class="{{ $body }}">
        @yield('content')
    </body>
</html>
