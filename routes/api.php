<?php

/** @var \Illuminate\Routing\Router $router */

$router->group(['namespace' => 'Resources'], function () use ($router) {
    $actions = [
        'only' => ['store', 'update', 'destroy'],
    ];

    // Server management
    $router->get('servers/{server}/test', 'ServerController@test')->name('servers.test');
    $router->post('servers/reorder', 'ServerController@reorder')->name('servers.reorder');
    $router->resource('servers', 'ServerController', $actions);
});

$router->get('projects/{id}', 'DeploymentController@project');

/*
Route::post('commands/reorder', [
    'as'   => 'commands.reorder',
    'uses' => 'CommandController@reorder',
]);

Route::get('projects/{id}/commands/{step}', [
    'as'   => 'commands.step',
    'uses' => 'CommandController@listing',
]);

$actions = [
    'only' => ['store', 'update', 'destroy'],
];
Route::resource('variables', 'VariableController', $actions);
Route::resource('commands', 'CommandController', $actions);
Route::resource('heartbeats', 'HeartbeatController', $actions);
Route::resource('notifications', 'NotificationController', $actions);
Route::resource('shared-files', 'SharedFilesController', $actions);
Route::resource('project-file', 'ProjectFileController', $actions);
Route::resource('notify-email', 'NotifyEmailController', $actions);
Route::resource('check-url', 'CheckUrlController', $actions);

Route::get('admin/templates/{id}/commands/{step}', [
    'as'   => 'admin.templates.commands.step',
    'uses' => 'CommandController@listing',
]);

*/
