<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Define the languages you want exported messages for
    |--------------------------------------------------------------------------
    */

    'locales' => [env('APP_LOCALE', 'en')],

    /*
    |--------------------------------------------------------------------------
    | Define the messages to export
    |--------------------------------------------------------------------------
    |
    | An array containing the keys of the messages you wish to make accessible
    | for the Javascript code.
    | Remember that the number of messages sent to the browser influences the
    | time the website needs to load. So you are encouraged to limit these
    | messages to the minimum you really need.
    |
    | Supports nesting:
    |   [ 'mynamespace' => ['test1', 'test2'] ]
    | for instance will be internally resolved to:
    |   ['mynamespace.test1', 'mynamespace.test2']
    |
    */

    'messages' => [
        'app',
        'checkUrls',
        'commands',
        'configFiles',
        'dashboard',
        'deployments',
        'emails',
        'groups',
        'heartbeats',
        'notifications',
        'notifyEmails',
        'projects',
        'servers',
        'sharedFiles',
        'templates',
        'users',
        'variables',
    ],

//    'messages' => [
//        'app'           => ['yes', 'no'],
//        'dashboard'     => ['pending', 'running', 'deployment_number'],
//        'deployments'   => ['completed', 'completed_with_errors', 'pending',
//                            'deploying', 'running', 'cancelled', 'failed', ],
//        'variables'     => ['create', 'edit'],
//        'projects'      => ['create', 'edit', 'finished', 'pending', 'deploying', 'failed', 'not_deployed'],
//        'checkUrls'     => ['create', 'edit', 'successful', 'failed', 'untested', 'length'],
//        'commands'      => ['create', 'edit'],
//        'groups'        => ['create', 'edit'],
//        'users'         => ['create', 'edit'],
//        'templates'     => ['create', 'edit'],
//        'sharedFiles'   => ['create', 'edit'],
//        'configFiles'   => ['create', 'edit'],
//        'notifications' => ['create', 'edit'],
//        'notifyEmails'  => ['create', 'edit'],
//        'servers'       => ['create', 'edit', 'successful', 'testing', 'failed', 'untested'],
//        'heartbeats'    => ['create', 'edit', 'ok', 'untested', 'missing', 'interval_10', 'interval_30',
//                            'interval_60', 'interval_120', 'interval_720', 'interval_1440', 'interval_10080', ]
//    ],

    /*
    |--------------------------------------------------------------------------
    | Set the keys of config properties you want to use in javascript.
    | Caution: Do not expose any configuration values that should be kept privately!
    |--------------------------------------------------------------------------
    */
    'config' => [
        /*'app.debug'  // example*/
    ],

    /*
    |--------------------------------------------------------------------------
    | Disables the config cache if set to true, so you don't have to
    | run `php artisan js-localization:refresh` each time you change configuration files.
    | Attention: Should not be used in production mode due to decreased performance.
    |--------------------------------------------------------------------------
    */
    'disable_config_cache' => env('APP_DEBUG', false),

];
