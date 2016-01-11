@extends('basic-layout')

@section('content')
    <div class="login-box">
        <div class="login-logo">
            <b>{{ Lang::get('app.name') }}</b>
        </div>

        <div class="login-box-body">
            <p class="login-box-msg">{{ Lang::get('auth.please_enter_code') }}</p>
            <form action="{{ route('auth.twofactor') }}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="{{ Lang::get('auth.authentication_code') }}" name="2fa_code" required />
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">{{ Lang::get('auth.sign_in') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
