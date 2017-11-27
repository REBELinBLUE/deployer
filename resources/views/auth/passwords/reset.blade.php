@extends('basic-layout')

@section('content')
    <div class="login-box">
        <div class="login-logo">
            <b>{{ trans('app.name') }}</b>
        </div>

        @include('_partials.errors')

        <div class="login-box-body">
            <p class="login-box-msg">{{ trans('auth.enter_password') }}</p>
            <form action="{{ route('auth.reset-password') }}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="form-group has-feedback">
                    <input type="email" class="form-control" placeholder="{{ trans('auth.email') }}" name="email" value="{{ old('email') }}" required />
                    <span class="fa fa-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" placeholder="{{ trans('auth.password') }}" name="password" required />
                    <span class="fa fa-lock form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" placeholder="{{ trans('auth.password_confirmation') }}" name="password_confirmation" required />
                    <span class="fa fa-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">{{ trans('auth.reset') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
