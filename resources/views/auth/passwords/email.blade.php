@extends('basic-layout')

@section('content')
    <div class="login-box">
        <div class="login-logo">
            <b>{{ trans('app.name') }}</b>
        </div>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @include('_partials.errors')

        <div class="login-box-body">
            <p class="login-box-msg">{{ trans('auth.enter_email') }}</p>
            <form action="{{ route('auth.reset-email') }}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <div class="form-group has-feedback">
                    <input type="email" class="form-control" placeholder="{{ trans('auth.email') }}" name="email" value="{{ old('email') }}" required />
                    <span class="fa fa-envelope form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">{{ trans('auth.send_link') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
