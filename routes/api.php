<?php

/** @var \Illuminate\Routing\Router $router */

$router->group(['namespace' => 'Resources'/*, 'prefix' => 'projects/{project}'*/], function () use ($router) {
    $actions = [
        'only' => ['store', 'update', 'destroy'],
    ];

    // Server management
    $router->post('servers/{server}/test', 'ServerController@test')->name('servers.test');
    $router->post('servers/reorder', 'ServerController@reorder')->name('servers.reorder');
    $router->get('servers/autocomplete', 'ServerController@autoComplete')->name('servers.autocomplete');
    $router->resource('servers', 'ServerController', $actions);

    $router->resource('variables', 'VariableController', $actions);
    $router->resource('heartbeats', 'HeartbeatController', $actions);
    $router->resource('notifications', 'ChannelController', $actions);
    $router->resource('shared-files', 'SharedFilesController', $actions);
    $router->resource('config-file', 'ConfigFileController', $actions);
    $router->resource('check-url', 'CheckUrlController', $actions);
    $router->resource('commands', 'CommandController', $actions);
});

$router->group(['namespace' => 'Resources', 'prefix' => 'projects/{project}'], function () use ($router) {
    $router->get('commands/{step}', 'CommandController@listing')->name('commands.step');
    $router->post('commands/reorder', 'CommandController@reorder')->name('commands.reorder');
});

$router->get('projects/{project}', 'DeploymentController@project');
