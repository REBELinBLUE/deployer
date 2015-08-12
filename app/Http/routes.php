<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */

Route::group(['middleware' => ['auth', 'minify']], function () {

    Route::get('/', 'DashboardController@index');
    Route::get('/timeline', 'DashboardController@timeline');

    Route::get('webhook/{projects}/refresh', 'WebhookController@refresh');

    Route::get('projects/{projects}', 'DeploymentController@project');

    Route::post('projects/{projects}/deploy', [
        'as'   => 'deploy',
        'uses' => 'DeploymentController@deploy',
    ]);

    // Deployment details
    Route::get('deployment/{deployments}', [
        'as'   => 'deployment',
        'uses' => 'DeploymentController@show',
    ]);

    Route::get('log/{log}', 'DeploymentController@log');

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

    // User profile managment

    Route::get('profile/index', [
        'as'   => 'profile.index',
        'uses' => 'ProfileController@index',
    ]);

    Route::post('profile/update', [
        'as'   => 'profile.update',
        'uses' => 'ProfileController@update',
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
});

// Change the login email
Route::get('profile/email/{token}', 'ProfileController@email');
Route::post('profile/update-email', 'ProfileController@changeEmail');

// Webhooks
Route::post('deploy/{hash}', [
    'as'   => 'webhook',
    'uses' => 'WebhookController@webhook',
]);

Route::get('cctray.xml', 'DashboardController@cctray');

Route::group(['namespace' => 'Resources'], function () {
    Route::get('heartbeat/{hash}', [
        'as'   => 'heartbeat',
        'uses' => 'HeartbeatController@ping',
    ]);
});

Route::controllers([
    'auth'     => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);
