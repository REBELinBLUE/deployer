<?php

/** @var \Illuminate\Routing\Router $router */

$router->get('cctray.xml', 'DashboardController@cctray')->name('cctray');
$router->get('heartbeat/{hash}', 'Resources\HeartbeatController@ping')->name('heartbeats');

$router->post('deploy/{hash}', [
    'middleware' => 'api',
    'uses'       => 'WebhookController@webhook',
])->name('webhook.deploy');
