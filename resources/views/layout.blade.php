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
        <meta name="socket_url" content="{{ env('SOCKET_URL') }}" />

        <script type="text/javascript">var Lang = {};</script>
    </head>
    <body class="skin-green">
        <div class="wrapper">

            @include('_partials.nav')

            @include('_partials.sidebar')

            <div class="content-wrapper">
                <section class="content-header">
                    @yield('right-buttons')
                    
                    <h1>{{ $title }}</h1>

                    @if(isset($breadcrumb))
                    <ol class="breadcrumb">
                        @foreach($breadcrumb as $entry)
                        <li><a href="{{ $entry['url'] }}">{{ $entry['label'] }}</a></li>
                        @endforeach
                        <li class="active">{{ $title }}</li>
                    </ol>
                    @endif
                </section>

                <section class="content" id="app">
                    @yield('content')
                </section>
            </div>
        </div>

        <script src="{{ elixir('js/vendor.js') }}"></script>
        <script src="{{ elixir('js/app.js') }}"></script>

        <script type="text/javascript">
            Lang.nav = {
                single_pending: '{{ Lang::choice('dashboard.pending', 1) }}',
                multi_pending: '{{ Lang::choice('dashboard.pending', '%s') }}',
                single_running: '{{ Lang::choice('dashboard.running', 1) }}',
                multi_running: '{{ Lang::choice('dashboard.running', '%s') }}'
            };
        </script>

        @yield('javascript')
    </body>
</html>