<?php

// Authentication routes
Route::group([
    'middleware' => ['web'],
], function () {

    Route::get('expired', [
        'uses' => 'LicenseController@expired',
    ]);
});
