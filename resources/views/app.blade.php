<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>{{ Lang::get('app.name') }}</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
        <link rel="author" href="humans.txt" />

        <!-- Style -->
        <link href="{{ elixir('css/vendor.css') }}" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="{{ elixir('js/ie.js') }}"></script>
        <![endif]-->

        <meta name="token" content="{{ Session::token() }}" />
        <meta name="socket_url" content="{{ config('deployer.socket_url') }}" />
        <meta name="jwt" content="{{ Session::get('jwt') }}" />
        <meta name="locale" content="{{ App::getLocale() }}" />
    </head>
    <body class="skin-{{ $theme }}">
        <div id="content"></div>

        <script src="{{ elixir('js/vendor.js') }}"></script>
        <script src="/js-localization/messages"></script>
        <script src="{{ elixir('js/app.js') }}"></script>
    </body>
</html>
