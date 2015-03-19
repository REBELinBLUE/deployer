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

// Projects
Route::get('deploy/{hash}', [
    'as'   => 'webhook',
    'uses' => 'ProjectController@webhook'
]);

Route::get('project/{id}', [
    'as'   => 'project',
    'uses' => 'ProjectController@details'
]);

Route::post('project/{id}/deploy', [
    'as'   => 'deploy',
    'uses' => 'ProjectController@deploy'
]);

Route::get('project/{id}/deploy/{deploy_id}', [
    'as'   => 'deployment',
    'uses' => 'ProjectController@deployment'
]);


// Servers
Route::resource('servers', 'ServerController', ['only' => ['show', 'store', 'update', 'destroy'] ]);
Route::get('servers/{id}/test', 'ServerController@test');

// Commands
Route::get('logs/{id}', 'CommandController@log');
Route::get('project/{id}/commands/{command}', 'CommandController@listing');


Route::controllers([
    'auth'     => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);
