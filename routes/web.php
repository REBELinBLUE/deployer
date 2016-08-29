<?php

/** @var \Illuminate\Routing\Router $router */

// Authentication routes
$router->get('login', 'Auth\LoginController@showLoginForm')->name('auth.login');
$router->post('login', 'Auth\LoginController@login');
$router->get('login/2fa', 'Auth\LoginController@showTwoFactorAuthenticationForm')->name('auth.twofactor');
$router->post('login/2fa', 'Auth\LoginController@twoFactorAuthenticate');
$router->post('logout', 'Auth\LoginController@logout');

// Password reset routes
$router->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('auth.reset-password');
$router->post('password/reset', 'Auth\ResetPasswordController@reset');
$router->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('auth.reset-email');
$router->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('auth.reset-confirm');

// Web application route
$router->get('{any?}', 'WebappController@index')->middleware(['auth', 'jwt'])->where('any', '.*');
