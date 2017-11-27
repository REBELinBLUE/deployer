@extends('basic-layout')

@section('content')
    <div class="login-box">
        <div class="login-logo">
            <b>{{ trans('app.name') }}</b>
        </div>

        @include('_partials.errors')

        <div class="login-box-body">
            <p class="login-box-msg">{{ trans('auth.please_sign_in') }}</p>
            <form action="{{ route('auth.login') }}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <div class="form-group has-feedback">
                    <input type="email" class="form-control" placeholder="{{ trans('auth.email') }}" name="email" value="{{ old('email') }}" required />
                    <span class="fa fa-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" placeholder="{{ trans('auth.password') }}" name="password" required />
                    <span class="fa fa-lock form-control-feedback"></span>
                </div>
                <div class="form-group checkbox">
                    <label>
                        <input type="checkbox" name="remember" value="on" />
                        {{ trans('auth.remember') }}
                    </label>
                </div>
                <div class="row">
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">{{ trans('auth.sign_in') }}</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="pull-right" id="forgotten-password">
            <p><a href="{{ route('auth.reset-password') }}">{{ trans('auth.forgotten') }}</a></p>
        </div>
    </div>
@stop
