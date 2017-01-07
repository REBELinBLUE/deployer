<?php

// Resource management
Route::group([
    'middleware' => ['web', 'auth', 'jwt'],
    'namespace'  => 'Resources',
], function () {
    Route::post('commands/reorder', [
        'as'   => 'commands.reorder',
        'uses' => 'CommandController@reorder',
    ]);

    Route::get('projects/{id}/commands/{step}', [
        'as'   => 'commands.step',
        'uses' => 'CommandController@listing',
    ]);

    Route::post('servers/reorder', [
        'as'    => 'servers.reorder',
        'uses'  => 'ServerController@reorder',
    ]);

    Route::get('servers/{id}/test', [
        'as'    => 'servers.test',
        'uses'  => 'ServerController@test',
    ]);

    Route::get('servers/autocomplete', [
        'as'    => 'servers.autocomplete',
        'uses'  => 'ServerController@autoComplete',
    ]);

    $actions = [
        'only' => ['store', 'update', 'destroy'],
    ];

    Route::resource('servers', 'ServerController', $actions);
    Route::resource('variables', 'VariableController', $actions);
    Route::resource('commands', 'CommandController', $actions);
    Route::resource('heartbeats', 'HeartbeatController', $actions);
    Route::resource('notifications', 'NotificationController', $actions);
    Route::resource('shared-files', 'SharedFilesController', $actions);
    Route::resource('config-file', 'ConfigFileController', $actions);
    Route::resource('notify-email', 'NotifyEmailController', $actions);
    Route::resource('check-url', 'CheckUrlController', $actions);
});
