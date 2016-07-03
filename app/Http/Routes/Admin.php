<?php
//
//// Administration
//Route::group([
//    'middleware' => ['web', 'auth', 'jwt'],
//    'prefix'     => 'admin',
//    'namespace'  => 'Admin',
//], function () {
//    Route::resource('templates', 'TemplateController', [
//        'only' => ['index', 'store', 'update', 'destroy', 'show'],
//    ]);
//
//    Route::resource('projects', 'ProjectController', [
//        'only' => ['index', 'store', 'update', 'destroy'],
//    ]);
//
//    Route::resource('users', 'UserController', [
//        'only' => ['index', 'store', 'update', 'destroy'],
//    ]);
//
//    Route::resource('groups', 'GroupController', [
//        'only' => ['index', 'store', 'update'],
//    ]);
//
//    Route::post('groups/reorder', [
//        'as'    => 'admin.groups.reorder',
//        'uses'  => 'GroupController@reorder',
//    ]);
//});
