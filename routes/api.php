<?php

/** @var \Illuminate\Routing\Router $router */

$router->group(['namespace' => 'Resources', 'prefix' => 'projects/{project}'], function () use ($router) {
    $actions = [
        'only' => ['store', 'update', 'destroy'],
    ];

    // Server management
    $router->get('servers/{server}/test', 'ServerController@test')->name('servers.test');
    $router->post('servers/reorder', 'ServerController@reorder')->name('servers.reorder');
    $router->resource('servers', 'ServerController', $actions);

//    $router->resource('variables', 'VariableController', $actions);
//    $router->resource('heartbeats', 'HeartbeatController', $actions);
//    $router->resource('notifications', 'NotificationController', $actions);
//    $router->resource('shared-files', 'SharedFilesController', $actions);
//    $router->resource('project-file', 'ProjectFileController', $actions);
//    $router->resource('notify-email', 'NotifyEmailController', $actions);
//    $router->resource('check-url', 'CheckUrlController', $actions);
//
//    $router->post('commands/reorder', 'CommandController@reorder')->name('commands.reorder');
//    $router->get('projects/{id}/commands/{step}', 'CommandController@listing')->name('commands.step');
//    $router->resource('commands', 'CommandController', $actions);
});

$router->get('projects/{project}', 'DeploymentController@project');
