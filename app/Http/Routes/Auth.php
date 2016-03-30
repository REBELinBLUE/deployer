<?php

// Authentication routes
Route::group([
    'middleware' => ['web', 'guest'],
    'namespace'  => 'Auth',
], function () {

    Route::get('login', [
        'middleware' => 'guest',
        'as'         => 'auth.login',
        'uses'       => 'AuthController@getLogin',
    ]);

    Route::post('login', [
        'middleware' => ['guest', 'throttle:10,10'],
        'as'         => 'auth.login-verify',
        'uses'       => 'AuthController@postLogin',
    ]);

    Route::get('login/2fa', [
        'as'   => 'auth.twofactor',
        'uses' => 'AuthController@getTwoFactorAuthentication',
    ]);

    Route::post('login/2fa', [
        'middleware' => 'throttle:10,10',
        'as'         => 'auth.twofactor-verify',
        'uses'       => 'AuthController@postTwoFactorAuthentication',
    ]);

    Route::get('password/reset/{token?}', [
        'as'   => 'auth.reset-password-confirm',
        'uses' => 'PasswordController@showResetForm',
    ]);

    Route::post('password/email', [
        'as'   => 'auth.request-password-reset',
        'uses' => 'PasswordController@sendResetLinkEmail',
    ]);

    Route::post('password/reset', [
        'as'   => 'auth.reset-password',
        'uses' => 'PasswordController@reset',
    ]);

});

Route::get('logout', [
    'middleware' => ['web', 'auth'],
    'as'         => 'auth.logout',
    'uses'       => 'Auth\AuthController@logout',
]);
