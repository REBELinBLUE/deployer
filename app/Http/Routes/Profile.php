<?php

// User profile
//Route::group([
//    'middleware' => ['web', 'auth', 'jwt'],
//], function () {
//    Route::get('profile', [
//        'as'   => 'profile.index',
//        'uses' => 'ProfileController@index',
//    ]);
//
//    Route::post('profile/update', [
//        'as'   => 'profile.update',
//        'uses' => 'ProfileController@update',
//    ]);
//
//    Route::post('profile/settings', [
//        'as'   => 'profile.settings',
//        'uses' => 'ProfileController@settings',
//    ]);
//
//    Route::post('profile/email', [
//        'as'   => 'profile.request-change-email',
//        'uses' => 'ProfileController@requestEmail',
//    ]);
//
//    Route::post('profile/upload', [
//        'as'   => 'profile.upload-avatar',
//        'uses' => 'ProfileController@upload',
//    ]);
//
//    Route::post('profile/avatar', [
//        'as'   => 'profile.avatar',
//        'uses' => 'ProfileController@avatar',
//    ]);
//
//    Route::post('profile/gravatar', [
//        'as'   => 'profile.gravatar',
//        'uses' => 'ProfileController@gravatar',
//    ]);
//
//    Route::post('profile/twofactor', [
//        'as'   => 'profile.twofactor',
//        'uses' => 'ProfileController@twoFactor',
//    ]);
//
//    Route::get('profile/email/{token}', [
//        'as'   => 'profile.confirm-change-email',
//        'uses' => 'ProfileController@email',
//    ]);
//
//    Route::post('profile/update-email', [
//        'as'   => 'profile.change-email',
//        'uses' => 'ProfileController@changeEmail',
//    ]);
//});
