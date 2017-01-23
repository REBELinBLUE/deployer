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
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <div class="form-group">
                        <label for="name">{{ Lang::get('users.name') }}</label>
                        <input type="text" name="name" value="{{ $logged_in_user->name }}" placeholder="{{ Lang::get('users.name') }}" class="form-control" />
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
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <div class="form-group">
                        <label for="skin">{{ Lang::get('users.skin') }}</label>
                        <select name="skin" id="skin" class="form-control">
                            @foreach ($settings->themes() as $colour)
                                @if (!str_contains($colour, '-light'))
                                    <option value="{{ $colour }}" @if ($colour === $theme) selected @endif>{{ Lang::get('users.' . $colour ) }}</option>
                                    <option value="{{ $colour }}-light" @if ($colour . '-light' === $theme) selected @endif>{{ Lang::get('users.with_sidebar', ['colour' => Lang::get('users.' . $colour)]) }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="scheme">{{ Lang::get('users.console') }}</label>
                        <select name="scheme" id="scheme" class="form-control">
                            <option value="" @if ($logged_in_user->scheme === null) selected @endif>{{ Lang::get('users.default' ) }}</option>
                            @foreach ($settings->schemes() as $scheme)
                                <option value="{{ $scheme }}" @if ($scheme === $logged_in_user->scheme) selected @endif>{{ ucwords(str_replace('-', ' ', $scheme)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="scheme">{{ Lang::get('users.language') }}</label>
                        <select name="language" id="language" class="form-control">
                            @foreach ($locales as $locale)
                                <option value="{{ $locale }}" @if ($locale === $logged_in_user->language) selected @endif>
                                    {{ locale_get_display_name($locale, $locale) }}
                                    @if ($locale !== $logged_in_user->language) ({{ locale_get_display_name($locale, $logged_in_user->language) }}) @endif
                                </option>
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
                        <img src="{{ $logged_in_user->avatar_url }}" class="current-avatar-preview" />

                        <div class="avatar-preview preview-md hide"></div>

                        <div id="avatar-save-buttons">
                            <button type="button" class="btn btn-primary btn-flat hide" id="save-avatar">{{ Lang::get('users.save') }}</button>
                            <button type="button" class="btn btn-warning btn-flat @if (!$logged_in_user->avatar) hide @endif " id="use-gravatar">{{ Lang::get('users.reset_gravatar') }}</button>
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

                @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('profile.twofactor') }}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />

                    <div class="pull-right auth-code @if (!$logged_in_user->has_two_factor_authentication and !old('google_code')) hide @endif ">
                        <img src="{{ $google_2fa_url }}" id="two-factor-img" class="img-responsive" />
                    </div>

                    <div class="checkbox">
                        <label for="two-factor-auth">
                            <input type="checkbox" id="two-factor-auth" name="two_factor" value="on" @if ($logged_in_user->has_two_factor_authentication or old('google_code')) checked @endif />
                            <strong>{{ Lang::get('users.enable_2fa') }}</strong>
                        </label>

                        <span class="help-block">
                            {!! Lang::get('users.2fa_help', ['url' => 'https://support.google.com/accounts/answer/1066447?hl=en']) !!}
                        </span>
                    </div>

                    @if (!$logged_in_user->has_two_factor_authentication)
                    <div class="form-group auth-code @if (!old('google_code')) hide @endif">

                        <label for="verify-google-code" style="clear:both">{{ Lang::get('users.verify_code') }}</label>
                        <input type="text" name="2fa_code" placeholder="{{ Lang::get('auth.authentication_code') }}" maxlength="6" class="form-control" id="verify-google-code" />
                        <input type="hidden" name="google_code" value="{{ $google_2fa_code }}" />

                        <span class="help-block">
                            {{ Lang::get('users.verify_help') }}
                        </span>
                    </div>
                    @endif

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-flat">{{ Lang::get('users.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
