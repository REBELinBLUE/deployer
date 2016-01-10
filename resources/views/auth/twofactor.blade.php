@extends('basic-layout')

@section('content')
    <div class="login-box">
        <div class="login-logo">
            <b>{{ Lang::get('app.name') }}</b>
        </div>
        @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>{{ Lang::get('auth.oops') }}</strong> {{ Lang::get('auth.problems') }}<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="login-box-body">
            <p class="login-box-msg">2fa</p>
            <form action="{{ route('two-factor') }}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="cpde" name="2fa_code" required />
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">validate</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
