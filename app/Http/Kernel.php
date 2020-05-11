<?php

namespace REBELinBLUE\Deployer\Http;

use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use REBELinBLUE\Deployer\Http\Middleware\Authenticate;
use REBELinBLUE\Deployer\Http\Middleware\EncryptCookies;
use REBELinBLUE\Deployer\Http\Middleware\IsAdmin;
use REBELinBLUE\Deployer\Http\Middleware\Locale;
use REBELinBLUE\Deployer\Http\Middleware\RedirectIfAuthenticated;
use REBELinBLUE\Deployer\Http\Middleware\RefreshJsonWebToken;
use REBELinBLUE\Deployer\Http\Middleware\TrimStrings;
use REBELinBLUE\Deployer\Http\Middleware\TrustProxies;
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
        TrustProxies::class,
        CheckForMaintenanceMode::class,
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
            // AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            Locale::class,
        ],
        'api' => [
            'throttle:60,1',
            // SubstituteBindings::class,
        ],
    ];

    // FIXME: What about verification controller/middleware

    /**
     * The application's route middleware.
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'          => Authenticate::class,
        // 'bindings'      => SubstituteBindings::class,
        // 'cache.headers' => SetCacheHeaders::class,
        // 'can'           => Authorize::class,
        // 'signed'        => ValidateSignature::class,
        'guest'         => RedirectIfAuthenticated::class,
        // 'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'jwt'           => RefreshJsonWebToken::class,
        'throttle'      => ThrottleRequests::class,
        'isadmin'       => IsAdmin::class,
        // 'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        StartSession::class,
        ShareErrorsFromSession::class,
        Authenticate::class,
        ThrottleRequests::class,
//        AuthenticateSession::class,
        SubstituteBindings::class,
//        Authorize::class,
    ];
}
