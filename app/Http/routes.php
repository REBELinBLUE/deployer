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

Route::get('project/{id}/commands/{command}', 'ProjectController@commands');
Route::resource('servers', 'ServerController', ['only' => ['show', 'store', 'update', 'destroy'] ]);
Route::get('servers/{id}/test', 'ServerController@test');

Route::controllers([
    'auth'     => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);
