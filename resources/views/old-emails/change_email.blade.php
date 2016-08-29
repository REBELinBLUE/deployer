@extends('emails.layout')

@section('content')
    <h1>{{ Lang::get('app.name') }}</h1>
    <br />
    <h2>{{ Lang::get('emails.login_reset') }}</h2>

    <br />
    {{ Lang::get('emails.request_email', ['username' => $name ]) }}: <a href="{{ route('profile.confirm-change-email', ['token' => $token]) }}">{{ route('profile.confirm-change-email', ['token' => $token]) }}</a>
@stop
