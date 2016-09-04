<?php

/** @var \Illuminate\Routing\Router $router */

$router->get('cctray.xml', 'WebappController@cctray')->name('cctray');
$router->get('heartbeat/{hash}', 'Resources\HeartbeatController@ping')->name('heartbeats');

$router->post('deploy/{hash}', [
    'middleware' => 'api',
    'uses'       => 'WebhookController@webhook',
])->name('webhook.deploy');
