<?php

// Webhooks
Route::get('cctray.xml', [
    'as'   => 'cctray',
    'uses' => 'DashboardController@cctray',
]);

Route::post('deploy/{hash}', [
    'as'         => 'webhook.deploy',
    'middleware' => 'api',
    'uses'       => 'WebhookController@webhook',
]);

Route::get('heartbeat/{hash}', [
    'as'   => 'heartbeats',
    'uses' => 'Resources\HeartbeatController@ping',
]);
