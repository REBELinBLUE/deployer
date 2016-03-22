<?php

// Authentication routes
Route::group([
    'middleware' => ['web', 'guest'],
    'namespace'  => 'Auth',
], function () {

    Route::get('login', [
        'middleware' => 'guest',
        'as'         => 'login',
        'uses'       => 'AuthController@getLogin',
    ]);

    Route::post('login', [
        'middleware' => ['guest', 'throttle:10,10'],
        'uses'       => 'AuthController@postLogin',
    ]);

    Route::get('login/2fa', [
        'as'   => 'auth.twofactor',
        'uses' => 'AuthController@getTwoFactorAuthentication',
    ]);

    Route::post('login/2fa', [
        'middleware' => 'throttle:10,10',
        'uses'       => 'AuthController@postTwoFactorAuthentication',
    ]);

    Route::get('password/reset/{token?}', 'PasswordController@showResetForm');
    Route::post('password/email', 'PasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'PasswordController@reset');

});

Route::group([
    'middleware' => ['web', 'auth'],
], function () {

    Route::get('logout', [
        'as'         => 'logout',
        'uses'       => 'Auth\AuthController@logout',
    ]);

});
