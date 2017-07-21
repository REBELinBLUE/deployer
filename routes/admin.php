<?php

/** @var \Illuminate\Routing\Router $router */

// Administration
$router->group(['namespace' => 'Admin', 'prefix' => 'admin'], function () use ($router) {
    $router->resource('projects', 'ProjectController', [
        'only' => ['index', 'store', 'update', 'destroy'],
        'as' => 'admin',
    ]);

    $router->resource('users', 'UserController', [
        'only' => ['index', 'store', 'update', 'destroy'],
        'as' => 'admin',
    ]);

    $router->resource('groups', 'GroupController', [
        'only' => ['index', 'store', 'update'],
        'as' => 'admin',
    ]);
    $router->post('groups/reorder', 'GroupController@reorder')->name('admin.groups.reorder');

    $router->resource('templates', 'TemplateController', [
        'only' => ['index', 'store', 'update', 'destroy', 'show'],
        'as' => 'admin',
    ]);
    $router->get('templates/{id}/commands/{step}', 'TemplateController@listing')->name('admin.templates.commands.step');
});
