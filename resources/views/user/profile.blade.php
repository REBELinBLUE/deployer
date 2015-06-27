@extends('layout')

@section('content')
<div class="row edit-profile">
    <div class="col-md-4">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">{{ Lang::get('users.basic') }}</h3>
            </div>
            <div class="box-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label for="name">{{ Lang::get('users.name') }}</label>
                        <input type="text" name="name" value="{{ $user->name }}" placeholder="{{ Lang::get('users.name') }}" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="password">{{ Lang::get('users.password') }}</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="{{ Lang::get('users.password_existing') }}">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">{{ Lang::get('users.password_confirm') }}</label>
                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="{{ Lang::get('users.password_existing') }}">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-flat">{{ Lang::get('users.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">{{ Lang::get('users.change_email') }}</h3>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <button type="button" class="btn btn-danger btn-flat" id="request-change-email">{{ Lang::get('users.request_confirm') }}</button>
                    <span class="help-block hide">A mail has been sent to you!</span>
                </div>
            </div>
            <div class="overlay hide">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="box box-defaut">
            <div class="box-header with-border">
                <h3 class="box-title">{{ Lang::get('users.avatar') }}</h3>
            </div>
            <div class="box-body"></div>
        </div>
    </div>
</div>
@endsection
