<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>{{ Lang::get('app.name') }}</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
        <link rel="author" href="humans.txt" />

        <!-- Style -->
        <link href="{{ elixir('css/vendor.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ elixir('css/app.css') }}" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="{{ elixir('js/ie.js') }}"></script>
        <![endif]-->

        <meta name="token" content="{{ Session::token() }}" />
    </head>
    <body class="skin-{{ $theme }}">
    <!--socket: {
    online: true,
    server: '{{ config('deployer.socket_url') }}',
    jwt: '{{ Session::get('jwt') }}'
    },-->
        <script type="text/javascript">
            // FIXME: Not sure if this is right
            const appConfig = {
                locale: '{{ App::getLocale() }}',
                user: {!! $logged_in_user->toJson() !!},
                outdated: {{ $is_outdated ? 'true' : 'false' }},
                latest: '{{ $current_version }}',
                version: '{{ $latest_version }}',
            };
        </script>

        <div id="content"></div>

        <script src="{{ elixir('js/vendor.js') }}"></script>
        <script src="/js-localization/messages"></script>
        <script src="{{ elixir('js/app.js') }}"></script>
    </body>
</html>
