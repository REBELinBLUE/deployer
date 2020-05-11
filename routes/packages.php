<?php

/** @var \Illuminate\Routing\Router $router */

use Lubusin\Decomposer\Controllers\DecomposerController;
use Melihovv\LaravelLogViewer\LaravelLogViewerController;

$router->group(['prefix' => 'admin'], function () use ($router) {
    // Laravel decomposer
    $router->get('sysinfo', DecomposerController::class . '@index');

    // Laravel log viewer
    $router->get('logs', LaravelLogViewerController::class . '@index');
});
