<?php

// Webapp
Route::group([
    'middleware' => ['web', 'auth', 'jwt'],
], function () {

    Route::get('{any?}', 'DashboardController@index')->where('any', '.*');

});
