<?php

// Dashboard
Route::group([
    'middleware' => ['web', 'auth'],
], function () {

    Route::get('/', [
        'as'   => 'dashboard',
        'uses' => 'DashboardController@index',
    ]);

    Route::get('timeline', 'DashboardController@timeline');

});
