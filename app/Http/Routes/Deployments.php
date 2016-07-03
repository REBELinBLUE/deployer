<?php

// Deployments
//Route::group([
//    'middleware' => ['web', 'auth', 'jwt'],
//], function () {
//    Route::get('webhook/{id}/refresh', [
//        'as'   => 'webhook.refresh',
//        'uses' => 'WebhookController@refresh',
//    ]);
//
//    Route::get('projects/{id}', [
//        'as'   => 'projects',
//        'uses' => 'DeploymentController@project',
//    ]);
//
//    Route::post('projects/{id}/deploy', [
//        'as'   => 'projects.deploy',
//        'uses' => 'DeploymentController@deploy',
//    ]);
//
//    Route::post('deployment/{id}/rollback', [
//        'as'   => 'deployments.rollback',
//        'uses' => 'DeploymentController@rollback',
//    ]);
//
//    Route::get('deployment/{id}/abort', [
//        'as'   => 'deployments.abort',
//        'uses' => 'DeploymentController@abort',
//    ]);
//
//    Route::get('deployment/{id}', [
//        'as'   => 'deployments',
//        'uses' => 'DeploymentController@show',
//    ]);
//
//    Route::get('log/{log}', [
//        'as'   => 'deployments.log',
//        'uses' => 'DeploymentController@log',
//    ]);
//});
