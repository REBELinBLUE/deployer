@extends('layout')

@section('content')
    <div class="login-box">
        <div class="login-logo">
            <b>{{ Lang::get('app.name') }}</b>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

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
            <p class="login-box-msg">{{ Lang::get('auth.please_sign_in') }}</p>
            <form action="{{ route('auth.login') }}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <div class="form-group has-feedback">
                    <input type="email" class="form-control" placeholder="{{ Lang::get('auth.email') }}" name="email" value="{{ old('email') }}" required />
                    <span class="fa fa-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" placeholder="{{ Lang::get('auth.password') }}" name="password" required />
                    <span class="fa fa-lock form-control-feedback"></span>
                </div>
                <div class="form-group checkbox">
                    <label>
                        <input type="checkbox" name="remember" value="on" />
                        {{ Lang::get('auth.remember') }}
                    </label>
                </div>
                <div class="row">
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">{{ Lang::get('auth.sign_in') }}</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="pull-right" id="forgotten-password">
            <p><a href="{{ route('auth.reset-password') }}">{{ Lang::get('auth.forgotten') }}</a></p>
        </div>
    </div>
@stop
