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

Route::get('/', [
    'middleware' => 'auth',
    'uses'       => 'DashboardController@index'
]);

// Webhooks
Route::post('deploy/{hash}', [ 
    'as'         => 'webhook',
    'middleware' => 'auth',
    'uses'       => 'WebhookController@webhook'
]);

Route::get('webhook/{id}/refresh', [
    'middleware' => 'auth',
    'uses'       => 'WebhookController@refresh'
]);

// Projects
// 
Route::resource('projects', 'ProjectController', ['only' => ['show', 'store', 'update', 'destroy']]);

Route::post('projects/{id}/deploy', [
    'as'         => 'deploy',
    'middleware' => 'auth',
    'uses'       => 'ProjectController@deploy'
]);

// Deployment details
Route::get('deployment/{id}', [ // FIXME Should this be on the deployment controller?
    'as'         => 'deployment',
    'middleware' => 'auth',
    'uses'       => 'DeploymentController@show'
]);

// Servers
Route::get('projects/{id}/servers', [
    'middleware' => 'auth',
    'uses'       => 'ProjectController@servers'
]);

Route::resource('servers', 'ServerController', ['only' => ['show', 'store', 'update', 'destroy']]);

Route::get('servers/{id}/test', [
    'middleware' => 'auth',
    'uses'       => 'ServerController@test'
]);

// Commands
Route::get('status/{id}', [
    'middleware' => 'auth',
    'uses'       => 'CommandController@status'
]);

Route::get('log/{id}', [
    'middleware' => 'auth',
    'uses'       => 'CommandController@log'
]);

Route::resource('commands', 'CommandController', ['only' => ['store', 'update', 'destroy']]);

Route::get('projects/{id}/commands/{command}', [
    'as'         => 'commands',
    'middleware' => 'auth',
    'uses'       => 'CommandController@listing'
]);

Route::controllers([
    'auth'     => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);
