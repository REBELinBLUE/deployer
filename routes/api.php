<?php

/** @var \Illuminate\Routing\Router $router */

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:api');

Route::group([
    'middleware' => ['web', 'auth', 'jwt'],
], function () {
    Route::get('api/projects/{id}', [
        'as'   => 'projects',
        'uses' => 'DeploymentController@project',
    ]);
});
