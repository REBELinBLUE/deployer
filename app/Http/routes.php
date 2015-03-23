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
// 
Route::resource('projects', 'ProjectController', ['only' => ['show', 'store', 'update', 'destroy']]);

Route::post('projects/{id}/deploy', [
    'as'   => 'deploy',
    'uses' => 'ProjectController@deploy'
]);

// Deployment details
Route::get('deployment/{id}', [ // FIXME Should this be on the deployment controller?
    'as'   => 'deployment',
    'uses' => 'DeploymentController@show'
]);

Route::get('status/{id}', 'DeploymentController@status');

// Servers
Route::get('projects/{id}/servers', 'ProjectController@servers');
Route::resource('servers', 'ServerController', ['only' => ['show', 'store', 'update', 'destroy']]);
Route::get('servers/{id}/test', 'ServerController@test');

// Commands
Route::get('logs/{id}', 'CommandController@log');

Route::resource('commands', 'CommandController', ['only' => ['store', 'update', 'destroy']]);
Route::get('projects/{id}/commands/{command}', [
    'as'   => 'commands',
    'uses' => 'CommandController@listing'
]);

Route::controllers([
    'auth'     => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);
