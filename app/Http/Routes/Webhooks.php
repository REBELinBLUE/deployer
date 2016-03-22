<?php

// Webhooks
Route::get('cctray.xml', 'DashboardController@cctray');

Route::post('deploy/{hash}', [
    'as'         => 'webhook',
    'middleware' => 'api',
    'uses'       => 'WebhookController@webhook',
]);

Route::get('heartbeat/{hash}', [
    'as'   => 'heartbeat',
    'uses' => 'Resources\HeartbeatController@ping',
]);
