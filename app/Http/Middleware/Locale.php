<?php

namespace REBELinBLUE\Deployer\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use MicheleAngioni\MultiLanguage\LanguageManager;

/**
 * Sets the applications locale based on the user's language.
 */
class Locale
{
    /**
     * @var LanguageManager
     */
    private $languageManager;

    /**
     * @var AuthFactory
     */
    private $auth;

    /**
     * Locale constructor.
     *
     * @param LanguageManager $languageManager
     * @param AuthFactory     $auth
     */
    public function __construct(LanguageManager $languageManager, AuthFactory $auth)
    {
        $this->languageManager = $languageManager;
        $this->auth            = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              $guard
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $user = $this->auth->guard($guard)->user();
        if ($user && $user->language) {
            $this->languageManager->setLocale($user->language);
        }

        return $next($request);
    }
}
