@extends('emails.layout')

@section('content')
    <h1>{{ Lang::get('app.name') }}</h1>
    <br />
    <h2>{{ Lang::get('emails.created') }}</h2>

    <br />
    {{ Lang::get('emails.login_at') }}: <a href="{{ url('/') }}">{{ url('/') }}</a>

    <br />
    <br />
    <ul>
        <li><strong>{{ Lang::get('emails.username') }}</strong>: {{ $email }}</li>
        <li><strong>{{ Lang::get('emails.password') }}</strong>: {{ $password }}</li>
    </ul>
@stop
