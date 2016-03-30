<?php

// Deployments
Route::group([
    'middleware' => ['web', 'auth', 'jwt'],
], function () {

    Route::get('webhook/{projects}/refresh', [
        'as'   => 'webhook.refresh',
        'uses' => 'WebhookController@refresh',
    ]);

    Route::get('projects/{projects}', [
        'as'   => 'projects',
        'uses' => 'DeploymentController@project',
    ]);

    Route::post('projects/{projects}/deploy', [
        'as'   => 'projects.deploy',
        'uses' => 'DeploymentController@deploy',
    ]);

    Route::post('deployment/{deployments}/rollback', [
        'as'   => 'deployments.rollback',
        'uses' => 'DeploymentController@rollback',
    ]);

    Route::get('deployment/{deployments}/abort', [
        'as'   => 'deployments.abort',
        'uses' => 'DeploymentController@abort',
    ]);

    Route::get('deployment/{deployments}', [
        'as'   => 'deployments',
        'uses' => 'DeploymentController@show',
    ]);

    Route::get('log/{log}', [
        'as'   => 'deployments.log',
        'uses' => 'DeploymentController@log',
    ]);

});
