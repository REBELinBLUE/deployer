@extends('emails.layout')

@section('content')
    <h1>{{ Lang::get('app.name') }}</h1>
    <br />
    <h2>{{ Lang::get('emails.reset') }}</h2>

    <br />

    {{ Lang::get('emails.reset_here') }}: <a href="{{ route('auth.reset-password-confirm', ['token' => $token]) }}">{{ route('auth.reset-password-confirm', ['token' => $token]) }}</a>
@stop
