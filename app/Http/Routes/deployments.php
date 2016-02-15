<?php

// Deployments
Route::group([
    'middleware' => ['web', 'auth', 'jwt'],
], function () {

    Route::get('webhook/{projects}/refresh', 'WebhookController@refresh');

    Route::get('projects/{projects}', 'DeploymentController@project');

    Route::post('projects/{projects}/deploy', [
        'as'   => 'deploy',
        'uses' => 'DeploymentController@deploy',
    ]);

    // FIXME: Should be post
    Route::get('deployment/{deployments}/rollback', [
        'as'   => 'rollback',
        'uses' => 'DeploymentController@rollback',
    ]);

    Route::get('deployment/{deployments}/abort', [
        'as'   => 'abort',
        'uses' => 'DeploymentController@abort',
    ]);

    Route::get('deployment/{deployments}', [
        'as'   => 'deployment',
        'uses' => 'DeploymentController@show',
    ]);

    Route::get('log/{log}', 'DeploymentController@log');

});
