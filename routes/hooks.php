<?php

/** @var \Illuminate\Routing\Router $router */
$router->get('cctray.xml', 'DashboardController@cctray')->name('cctray');
$router->get('heartbeat/{hash}', 'Resources\HeartbeatController@ping')->name('heartbeats');

$router->middleware('api')->post('deploy/{hash}', 'WebhookController@webhook')->name('webhook.deploy');
