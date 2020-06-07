<?php

/** @var \Illuminate\Routing\Router $router */

use Melihovv\LaravelLogViewer\LaravelLogViewerController;

$router->group(['prefix' => 'admin'], function () use ($router) {
    // Laravel log viewer
    $router->get('logs', LaravelLogViewerController::class . '@index');
});
