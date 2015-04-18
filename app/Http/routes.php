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

Route::group(['middleware' => 'auth'], function () {

    Route::get('/', 'DashboardController@index');
    Route::get('webhook/{projects}/refresh', 'WebhookController@refresh');
    Route::get('projects/{projects}', 'ProjectController@show');

    Route::post('projects/{projects}/deploy', [
        'as'   => 'deploy',
        'uses' => 'ProjectController@deploy'
    ]);

    // Deployment details
    Route::get('deployment/{deployments}', [ // FIXME Should this be on the deployment controller?
        'as'   => 'deployment',
        'uses' => 'DeploymentController@show'
    ]);

    Route::resource('servers', 'ServerController', [
        'only' => ['show', 'store', 'update', 'destroy']
    ]);

    Route::resource('notifications', 'NotificationController', [
        'only' => ['store', 'update', 'destroy']
    ]);

    Route::get('servers/{servers}/test', 'ServerController@test');

    Route::get('status/{log}', 'CommandController@status');
    Route::get('log/{log}', 'CommandController@log');

    Route::resource('commands', 'CommandController', [
        'only' => ['store', 'update', 'destroy']
    ]);

    Route::post('commands/reorder', 'CommandController@reorder');

    Route::get('projects/{projects}/commands/{step}', [
        'as'   => 'commands',
        'uses' => 'CommandController@listing'
    ]);

    Route::group(['prefix' => 'admin'], function () {

        Route::resource('projects', 'ProjectController', [
            'only' => ['index', 'store', 'update', 'destroy']
        ]);

        Route::resource('templates', 'TemplateController', [
            'only' => ['index', 'store', 'update', 'destroy']
        ]);

        Route::resource('templates/{template}/commands', 'TemplateCommandController', [
            'only' => ['index', 'store', 'update', 'destroy']
        ]);

        Route::resource('users', 'UserController', [
            'only' => ['index', 'store', 'update', 'destroy']
        ]);

        Route::resource('groups', 'GroupController', [
            'only' => ['index', 'store', 'update']
        ]);

    });

});

// Webhooks
Route::post('deploy/{hash}', [
    'as'   => 'webhook',
    'uses' => 'WebhookController@webhook'
]);

Route::controllers([
    'auth'     => 'Auth\AuthController',
    'password' => 'Auth\PasswordController'
]);
