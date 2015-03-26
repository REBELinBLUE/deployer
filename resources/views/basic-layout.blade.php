<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Deployer</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <!-- Style -->
        <link href="{{ elixir('css/style.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ elixir('css/admin.css') }}" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="login-page">

        @yield('content')

        <script src="{{ elixir('js/style.js') }}"></script>
        <script src="{{ elixir('js/admin.js') }}"></script>
    </body>
</html>