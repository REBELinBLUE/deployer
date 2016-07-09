<?php

// Dashboard
Route::group([
    'middleware' => ['web', 'auth', 'jwt'],
], function () {
    Route::get('api/projects', 'APIController@projects');
    Route::get('api/groups', 'APIController@groups');
});
