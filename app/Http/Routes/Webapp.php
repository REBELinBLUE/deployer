<?php
// Dashboard
Route::group([
    'middleware' => ['web', 'auth', 'jwt'],
], function () {
    // FIXME: These should maybe be one function?
    Route::get('app/running', 'DashboardController@running');
    Route::get('app/dashboard', 'DashboardController@projects');


    Route::get('{any?}', function () {
        return view('app');
    })->where('any', '.*');

});
