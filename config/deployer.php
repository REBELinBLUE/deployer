<?php

return [

    // Deployer specific config
    'socket_url'         => env('SOCKET_URL', 'http://deployer.app'),
    'theme'              => env('APP_THEME', 'green'),
    'github_oauth_token' => env('GITHUB_OAUTH_TOKEN', false),

    'guzzle' => [
        'timeout'         => env('GUZZLE_HTTP_TIMEOUT', 5),
        'verify'          => env('GUZZLE_HTTP_VERIFY', false),
        'connect_timeout' => env('GUZZLE_HTTP_CONNECT_TIMEOUT', 10),
        'proxy'           => env('GUZZLE_HTTP_PROXY', false),
    ],

];
