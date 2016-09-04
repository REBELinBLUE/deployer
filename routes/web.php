<?php

/** @var \Illuminate\Routing\Router $router */

// Authentication routes
$router->group(['namespace' => 'Auth'], function () use ($router) {
    $router->get('login', 'LoginController@showLoginForm')->name('auth.login');
    $router->post('login', 'LoginController@login');
    $router->get('login/2fa', 'LoginController@showTwoFactorAuthenticationForm')->name('auth.twofactor');
    $router->post('login/2fa', 'LoginController@twoFactorAuthenticate');
    $router->post('logout', 'LoginController@logout');

    // Password reset routes
    $router->get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('auth.reset-password');
    $router->post('password/reset', 'ResetPasswordController@reset');
    $router->post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('auth.reset-email');
    $router->get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('auth.reset-confirm');
});

// Include the API routes inside an app path and add the authentication middleware to protect them
$router->group(['middleware' => ['auth', 'jwt'], 'prefix' => 'app'], function () use ($router) {
    require base_path('routes/api.php');
});

// Web application route
$router->get('{any?}', 'WebappController@index')->middleware(['auth', 'jwt'])->where('any', '.*');
