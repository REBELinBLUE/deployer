<?php

// Webapp
Route::get('{any?}', [
    'middleware' => ['web', 'auth', 'jwt'],
    'uses' => 'DashboardController@index'
])->where('any', '.*');
