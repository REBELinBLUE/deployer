<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Package Helpers
    |--------------------------------------------------------------------------
    |
    | This package comes with many pre-built helpers. Activate the ones
    | you wish to use by selecting them here. Valid options include:
    |
    | buttons, confirm, datetime, feedback, formatter, hint, image, money, validation
    |
    */

    'package_helpers' => [],

    /*
    |--------------------------------------------------------------------------
    | Custom Helpers
    |--------------------------------------------------------------------------
    |
    | You are also encouraged to write your own helpers. If you prefer a
    | mapper based inclusion, you can selectively activate your custom
    | helpers by adding them here. Otherwise all PHP files in the
    | Helpers directory will be automatically loaded.
    |
    */

    'custom_helpers' => [],

    /*
    |--------------------------------------------------------------------------
    | Directory
    |--------------------------------------------------------------------------
    |
    | By default this package will look in the application's 'Helpers'
    | directory. However, you may choose to override the directory.
    |
    */

    'directory' => 'Helpers',

    /*
    |--------------------------------------------------------------------------
    | Default Timezone
    |--------------------------------------------------------------------------
    |
    | The 'datetime' helper provides an easy way to format all of your Datetime
    | instances according to a runtime timezone. As a fallback, you may
    | enter your applications's default timezone here.
    |
    */

    'default_timezone' => env('APP_TIMEZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | Default Image
    |--------------------------------------------------------------------------
    |
    | The 'image' helper provides a simple default image when no image is
    | passed to it. Here you can define which image you would like to
    | use as the fallback image. The path is relative to the
    | resource root, or 'public' directory.
    |
    */

    'default_image' => 'images/noImage.svg',

];
