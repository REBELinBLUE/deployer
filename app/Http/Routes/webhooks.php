<?php

// Webhooks
Route::get('cctray.xml', 'DashboardController@cctray');

Route::group([
    'middleware' => 'api',
], function () {

    Route::group(['namespace' => 'Resources'], function () {

        Route::get('heartbeat/{hash}', [
            'as'   => 'heartbeat',
            'uses' => 'HeartbeatController@ping',
        ]);

    });

    Route::post('deploy/{hash}', [
        'as'   => 'webhook',
        'uses' => 'WebhookController@webhook',
    ]);

});
