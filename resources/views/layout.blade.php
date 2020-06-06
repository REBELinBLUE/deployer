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

        <meta name="csrf-token" content="{{ Session::token() }}" />
        <meta name="socket-url" content="{{ config('deployer.socket_url') }}" />
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
                        <h4><i class="icon fa fa-ban"></i> {{ trans('app.socket_error') }}</h4>
                        {!! trans('app.socket_error_info') !!}
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
                </section>
            </div>
        </div>

        <script src="{{ mix('/js/manifest.js') }}"></script>
        <script src="{{ mix('/js/vendor.js') }}"></script>
        <script src="{{ mix('/js/app.js') }}"></script>

        @stack('templates')
        @stack('javascript')
    </body>
</html>
