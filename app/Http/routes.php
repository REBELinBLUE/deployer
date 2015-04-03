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

    Route::get('/', [
        'uses'  => 'DashboardController@index'
    ]);

    Route::get('webhook/{id}/refresh', [
        'uses' => 'WebhookController@refresh'
    ]);


    Route::get('projects/{id}', [
        'uses' => 'ProjectController@show'
    ]);
    
    Route::resource('admin/projects', 'ProjectController', [
        'only' => ['index', 'store', 'update', 'destroy']
    ]);

    Route::post('projects/{id}/deploy', [
        'as'   => 'deploy',
        'uses' => 'ProjectController@deploy'
    ]);

    // Deployment details
    Route::get('deployment/{id}', [ // FIXME Should this be on the deployment controller?
        'as'   => 'deployment',
        'uses' => 'DeploymentController@show'
    ]);

    // Servers
    Route::get('projects/{id}/servers', [
        'uses' => 'ProjectController@servers'
    ]);

    Route::resource('servers', 'ServerController', [
        'only' => ['show', 'store', 'update', 'destroy']
    ]);

    Route::resource('notifications', 'NotificationController', [
        'only' => ['show', 'store', 'update', 'destroy']
    ]);

    Route::get('servers/{id}/test', [
        'uses' => 'ServerController@test'
    ]);

    // Commands
    Route::get('status/{id}', [
        'uses' => 'CommandController@status'
    ]);

    Route::get('log/{id}', [
        'uses' => 'CommandController@log'
    ]);

    Route::resource('commands', 'CommandController', [
        'only' => ['store', 'update', 'destroy']
    ]);

    Route::post('commands/reorder', [
        'uses' => 'CommandController@reorder'
    ]);

    Route::get('projects/{id}/commands/{command}', [
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
    'as'         => 'webhook',
    'uses'       => 'WebhookController@webhook'
]);

Route::controllers([
    'auth'     => 'Auth\AuthController',
    'password' => 'Auth\PasswordController'
]);
