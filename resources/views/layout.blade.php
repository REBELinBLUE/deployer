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
        <meta name="socket_url" content="{{ config('deployer.socket_url') }}" />
        <meta name="jwt" content="{{ Session::get('jwt') }}" />
        <meta name="locale" content="{{ App::getLocale() }}" />
    </head>
    <body class="skin-{{ $theme }}">
        <div class="wrapper">

            @include('_partials.nav')

            @include('_partials.sidebar')

            <div class="content-wrapper">
                <section class="content-header">
                    @yield('right-buttons')

                    <h1>{{ $title }} @if(isset($subtitle)) <small>{{ $subtitle }}</small>@endif</h1>

                    @include('_partials.update')

                    <div class="alert alert-danger" id="socket_offline">
                        <h4><i class="icon fa fa-ban"></i> {{ Lang::get('app.socket_error') }}</h4>
                        {!! Lang::get('app.socket_error_info') !!}
                    </div>

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

                    <pre>@{{ $data | json }}</pre>
                </section>
            </div>
        </div>

        @stack('templates')

        <script src="{{ elixir('js/vendor.js') }}"></script>
        <script src="/js-localization/messages"></script>
        <script src="{{ elixir('js/app.js') }}"></script>

        @stack('javascript')
    </body>
</html>
