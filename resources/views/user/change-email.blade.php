@extends('basic-layout')

@section('content')
    <div class="login-box">
        <div class="login-logo">
            <b>{{ Lang::get('app.name') }}</b>
        </div>
        <div class="login-box-body">
            <p class="login-box-msg">{{ Lang::get('users.enter_email') }}</p>
            <form action="{{ url('/profile/update-email') }}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="form-group has-feedback">
                    <input type="email" class="form-control" placeholder="{{ Lang::get('users.email') }}" name="email"  value="{{ old('email') }}" required />
                    <span class="fa fa-envelope form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">{{ Lang::get('users.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
