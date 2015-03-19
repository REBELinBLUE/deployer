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

Route::get('/', 'DashboardController@index');

// Webhooks
Route::post('deploy/{hash}', [ 
    'as'   => 'webhook',
    'uses' => 'WebhookController@webhook'
]);

Route::get('webhook/{id}/refresh', [
    'uses' => 'WebhookController@refresh'
]);

// Projects
Route::get('project/{id}', [
    'as'   => 'project',
    'uses' => 'ProjectController@details'
]);

Route::post('project/{id}/deploy', [
    'as'   => 'deploy',
    'uses' => 'ProjectController@deploy'
]);

// Deployment details
Route::get('deployment/{id}', [ // FIXME Should this be on the deployment controller?
    'as'   => 'deployment',
    'uses' => 'DeploymentController@show'
]);

// Servers
Route::resource('servers', 'ServerController', ['only' => ['show', 'store', 'update', 'destroy'] ]);
Route::get('servers/{id}/test', 'ServerController@test');

// Commands
Route::get('logs/{id}', 'CommandController@log');
Route::post('commands/{command}', 'CommandController@store'); // FIXME: Can we use the resourcecontroller for this?
Route::get('project/{id}/commands/{command}', [
    'as'   => 'commands',
    'uses' => 'CommandController@listing'
]);

Route::controllers([
    'auth'     => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);
