<?php

namespace REBELinBLUE\Deployer\Http;

use REBELinBLUE\Deployer\Http\Middleware\TrustProxies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use REBELinBLUE\Deployer\Http\Middleware\Authenticate;
use REBELinBLUE\Deployer\Http\Middleware\EncryptCookies;
use REBELinBLUE\Deployer\Http\Middleware\Locale;
use REBELinBLUE\Deployer\Http\Middleware\RedirectIfAuthenticated;
use REBELinBLUE\Deployer\Http\Middleware\RefreshJsonWebToken;
use REBELinBLUE\Deployer\Http\Middleware\TrimStrings;
use REBELinBLUE\Deployer\Http\Middleware\VerifyCsrfToken;

/**
 * Kernel class.
 */
class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        CheckForMaintenanceMode::class,
        TrustProxies::class,
        ValidatePostSize::class,
        TrimStrings::class,
        ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            Locale::class,
        ],
        'api' => [
            'throttle:60,1',
        ],
    ];

    /**
     * The application's route middleware.
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'       => Authenticate::class,
        'guest'      => RedirectIfAuthenticated::class,
        'jwt'        => RefreshJsonWebToken::class,
        'throttle'   => ThrottleRequests::class,
    ];
}
