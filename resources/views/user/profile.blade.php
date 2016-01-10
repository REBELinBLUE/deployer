@extends('layout')

@section('content')
<div class="row edit-profile">
    <div class="col-md-4">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">{{ Lang::get('users.basic') }}</h3>
            </div>
            <div class="box-body">
                <form action="{{ route('profile.update') }}" method="post">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label for="name">{{ Lang::get('users.name') }}</label>
                        <input type="text" name="name" value="{{ $user->name }}" placeholder="{{ Lang::get('users.name') }}" class="form-control" />
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

        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">{{ Lang::get('users.settings') }}</h3>
            </div>
            <div class="box-body">
                <form action="{{ route('profile.settings') }}" method="post">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label for="skin">{{ Lang::get('users.theme') }}</label>
                        <select name="skin" id="skin" class="form-control">
                            @foreach (['yellow', 'red', 'green', 'purple', 'blue'] as $colour)
                                <option value="{{ $colour }}" @if ($colour === $theme) selected @endif>{{ Lang::get('users.' . $colour )}}</option>
                                <option value="{{ $colour }}-light" @if ($colour . '-light' === $theme) selected @endif>{{ Lang::get('users.with_sidebar', ['colour' => Lang::get('users.' . $colour)])}}</option>
                            @endforeach
                        </select>
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
                    <span class="help-block hide">{{ Lang::get('users.email_sent') }}</span>
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
            <div class="box-body">
                <div class="row">

                    <div class="col-md-12 avatar-message">
                        <div class="alert alert-success hide" role="alert">{{ Lang::get('users.avatar_success') }}</div>
                        <div class="alert alert-danger hide" role="alert">{{ Lang::get('users.avatar_failed') }}</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="avatar">
                            <img src="{{ url('upload/picture.jpg') }}" class="img-rounded img-responsive" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <img src="{{ Auth::user()->avatar_url }}" class="current-avatar-preview" />

                        <div class="avatar-preview preview-md hide"></div>

                        <div id="avatar-save-buttons">
                            <button type="button" class="btn btn-primary btn-flat hide" id="save-avatar">{{ Lang::get('users.save') }}</button>
                            <button type="button" class="btn btn-warning btn-flat @if(!$user->avatar) hide @endif" id="use-gravatar">{{ Lang::get('users.reset_gravatar') }}</button>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary btn-flat" id="upload">{{ Lang::get('users.upload') }}</button>
                    </div>
                </div>
            </div>
            <div class="overlay" id="upload-overlay">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>

        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">{{ Lang::get('users.2fa') }}</h3>
            </div>
            <div class="box-body">
                <form action="{{ route('profile.twofactor') }}" method="post">
                    {!! csrf_field() !!}

                    @if ($user->hasTwoFactorAuthentication())
                        <div class="pull-right">
                            <img src="{{ $google_2fa_url }}" class="img-responsive" />
                        </div>
                    @endif

                    <div class="checkbox">
                        <label for="two-factor-auth">
                            <input type="checkbox" id="two-factor-auth" name="two_factor" value="on"  @if ($user->hasTwoFactorAuthentication()) checked @endif />
                            {{ Lang::get('users.enable_2fa') }}
                        </label>

                        <span class="help-block">
                            {!! Lang::get('users.2fa_help') !!}
                        </span>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-flat">{{ Lang::get('users.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
