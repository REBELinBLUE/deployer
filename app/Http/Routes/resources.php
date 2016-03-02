<?php

// Resource management
Route::group([
    'middleware' => ['web', 'auth', 'jwt'],
    'namespace'  => 'Resources',
], function () {

    Route::post('commands/reorder', 'CommandController@reorder');

    Route::get('projects/{projects}/commands/{step}', [
        'as'   => 'commands',
        'uses' => 'CommandController@listing',
    ]);

    Route::post('servers/reorder', 'ServerController@reorder');
    Route::get('servers/{servers}/test', 'ServerController@test');

    $actions = [
        'only' => ['store', 'update', 'destroy'],
    ];

    Route::resource('servers', 'ServerController', $actions);
    Route::resource('variables', 'VariableController', $actions);
    Route::resource('commands', 'CommandController', $actions);
    Route::resource('heartbeats', 'HeartbeatController', $actions);
    Route::resource('notifications', 'NotificationController', $actions);
    Route::resource('shared-files', 'SharedFilesController', $actions);
    Route::resource('project-file', 'ProjectFileController', $actions);
    Route::resource('notify-email', 'NotifyEmailController', $actions);
    Route::resource('check-url', 'CheckUrlController', $actions);

    Route::get('admin/templates/{projects}/commands/{step}', [
        'as'   => 'template.commands',
        'uses' => 'CommandController@listing',
    ]);

});
