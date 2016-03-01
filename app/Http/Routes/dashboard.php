<?php

// Dashboard
Route::group([
    'middleware' => ['web', 'auth', 'jwt'],
], function () {

    Route::get('/', [
        'as'   => 'dashboard',
        'uses' => 'DashboardController@index',
    ]);

    Route::get('timeline', 'DashboardController@timeline');

});
