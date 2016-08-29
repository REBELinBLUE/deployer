@include('js-localization::head')
@extends('layout')

@section('content')
    <script type="text/javascript">
        window.__PRELOADED_STATE__ = {
            deployer: {
                locale: '{{ App::getLocale() }}',
                user: {!! $logged_in_user->toJson() !!},
                outdated: {{ $is_outdated ? 'true' : 'false' }},
                latest: '{{ $current_version }}',
                version: '{{ $latest_version }}',
                token: '{{ Session::token() }}',
            },
            socket: {
                server: '{{ config('deployer.socket_url') }}',
                jwt: '{{ Session::get('jwt') }}'
            },
            navigation: {
                running: {!! $deploying->toJson() !!},
                pending: {!! $pending->toJson() !!},
                projects: {!! $projects !!},
                groups: {!! $groups !!}
            },
            dashboard: {
                timeline: {!! $latest !!}
            }
        };
    </script>

    <div id="content"></div>

    <script src="{{ webpack('js/vendor.js') }}"></script>
    @yield('js-localization.head')
    <script src="{{ webpack('js/app.js') }}"></script>
@stop
