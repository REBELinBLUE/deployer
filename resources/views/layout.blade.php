<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>{{ Lang::get('app.name') }}</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />

        <!-- Style -->
        <link href="{{ elixir('css/vendor.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ elixir('css/app.css') }}" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <meta name="token" content="{{ Session::token() }}" />
    </head>
    <body class="skin-green">
        <div class="wrapper">

            @include('._partials.nav')

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

        @yield('javascript')
    </body>
</html>