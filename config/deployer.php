<?php

return [

    // Deployer specific config
    'socket_url' => env('SOCKET_URL', 'http://deploy.app'),
    'theme' => env('APP_THEME', 'green'),
    'github_oauth_token' => env('GITHUB_OAUTH_TOKEN', false),

];
