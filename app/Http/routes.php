<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('/', [
        'as'   => 'dashboard',
        'uses' => 'DashboardController@index'
    ]);

    Route::get('timeline', 'DashboardController@timeline');

    Route::get('webhook/{projects}/refresh', 'WebhookController@refresh');

    Route::get('projects/{projects}', 'DeploymentController@project');

    Route::post('projects/{projects}/deploy', [
        'as'   => 'deploy',
        'uses' => 'DeploymentController@deploy',
    ]);

    // Abort deployment
    Route::get('deployment/{deployments}/abort', [
        'as'   => 'abort',
        'uses' => 'DeploymentController@abort',
    ]);

    // Deployment details
    Route::get('deployment/{deployments}', [
        'as'   => 'deployment',
        'uses' => 'DeploymentController@show',
    ]);

    Route::get('log/{log}', 'DeploymentController@log');

    // User profile managment
    Route::get('profile', [
        'as'   => 'profile.index',
        'uses' => 'ProfileController@index',
    ]);

    Route::post('profile/update', [
        'as'   => 'profile.update',
        'uses' => 'ProfileController@update',
    ]);

    Route::post('profile/settings', [
        'as'   => 'profile.settings',
        'uses' => 'ProfileController@settings',
    ]);

    Route::post('profile/email', [
        'as'   => 'profile.request_change_email',
        'uses' => 'ProfileController@requestEmail',
    ]);

    Route::post('profile/upload', [
        'as'   => 'profile.upload_avatar',
        'uses' => 'ProfileController@upload',
    ]);

    Route::post('profile/avatar', [
        'as'   => 'profile.avatar',
        'uses' => 'ProfileController@avatar',
    ]);

    Route::post('profile/gravatar', [
        'as'   => 'profile.gravatar',
        'uses' => 'ProfileController@gravatar',
    ]);

    Route::post('profile/twofactor', [
        'as'   => 'profile.twofactor',
        'uses' => 'ProfileController@twoFactor',
    ]);

    Route::get('profile/email/{token}', 'ProfileController@email');
    Route::post('profile/update-email', 'ProfileController@changeEmail');

    // Resource management
    Route::group(['namespace' => 'Resources'], function () {

        Route::post('commands/reorder', 'CommandController@reorder');

        Route::get('projects/{projects}/commands/{step}', [
            'as'   => 'commands',
            'uses' => 'CommandController@listing',
        ]);

        Route::post('servers/reorder', 'ServerController@reorder');
        Route::get('servers/{servers}/test', 'ServerController@test');

        $actions = [
            'only' => ['store', 'update', 'destroy'],
        ];

        Route::resource('servers', 'ServerController', $actions);
        Route::resource('variables', 'VariableController', $actions);
        Route::resource('commands', 'CommandController', $actions);
        Route::resource('heartbeats', 'HeartbeatController', $actions);
        Route::resource('notifications', 'NotificationController', $actions);
        Route::resource('shared-files', 'SharedFilesController', $actions);
        Route::resource('project-file', 'ProjectFileController', $actions);
        Route::resource('notify-email', 'NotifyEmailController', $actions);
        Route::resource('check-url', 'CheckUrlController', $actions);

        Route::get('admin/templates/{projects}/commands/{step}', [
            'as'   => 'template.commands',
            'uses' => 'CommandController@listing',
        ]);
    });

    // Administration
    Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
        Route::resource('templates', 'TemplateController', [
            'only' => ['index', 'store', 'update', 'destroy', 'show'],
        ]);

        Route::resource('projects', 'ProjectController', [
            'only' => ['index', 'store', 'update', 'destroy'],
        ]);

        Route::resource('users', 'UserController', [
            'only' => ['index', 'store', 'update', 'destroy'],
        ]);

        Route::resource('groups', 'GroupController', [
            'only' => ['index', 'store', 'update'],
        ]);
    });
});

Route::group(['middleware' => 'web', 'namespace' => 'Auth'], function () {

    Route::controllers([
        'password' => 'PasswordController',
    ]);

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
        'as'   => 'two-factor',
        'uses' => 'AuthController@getTwoFactorAuthentication',
    ]);

    Route::post('login/2fa', [
        'middleware' => 'throttle:10,10',
        'uses'       => 'AuthController@postTwoFactorAuthentication',
    ]);

    Route::get('logout', [
        'middleware' => 'auth',
        'as'         => 'logout',
        'uses'       => 'AuthController@logout',
    ]);
});

Route::get('cctray.xml', 'DashboardController@cctray');

Route::group(['middleware' => 'api'], function () {
    Route::group(['namespace' => 'Resources'], function () {
        Route::get('heartbeat/{hash}', [
            'as'   => 'heartbeat',
            'uses' => 'HeartbeatController@ping',
        ]);
    });

    // Webhooks
    Route::post('deploy/{hash}', [
        'as'   => 'webhook',
        'uses' => 'WebhookController@webhook',
    ]);
});
