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
    Route::get('webhook/{project}/refresh', 'WebhookController@refresh');
    Route::get('projects/{project}', 'ProjectController@show');
    
    Route::resource('admin/projects', 'ProjectController', [
        'only' => ['index', 'store', 'update', 'destroy']
    ]);

    Route::post('projects/{project}/deploy', [
        'as'   => 'deploy',
        'uses' => 'ProjectController@deploy'
    ]);

    // Deployment details
    Route::get('deployment/{deployment}', [ // FIXME Should this be on the deployment controller?
        'as'   => 'deployment',
        'uses' => 'DeploymentController@show'
    ]);

    // Servers
    Route::get('projects/{project}/servers', 'ProjectController@servers');

    Route::resource('servers', 'ServerController', [
        'only' => ['show', 'store', 'update', 'destroy']
    ]);

    Route::resource('notifications', 'NotificationController', [
        'only' => ['store', 'update', 'destroy']
    ]);

    Route::get('servers/{server}/test', 'ServerController@test');

    // Commands
    Route::get('status/{id}', 'CommandController@status');

    Route::get('log/{id}', 'CommandController@log');

    Route::resource('commands', 'CommandController', [
        'only' => ['store', 'update', 'destroy']
    ]);

    Route::post('commands/reorder', 'CommandController@reorder');

    Route::get('projects/{project}/commands/{command}', [
        'as'   => 'commands',
        'uses' => 'CommandController@listing'
    ]);

    Route::resource('admin/users', 'UserController', [
        'only' => ['index', 'store', 'update', 'destroy']
    ]);

    Route::resource('admin/groups', 'GroupController', [
        'only' => ['index', 'store', 'update']
    ]);
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
