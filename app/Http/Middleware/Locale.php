<?php

namespace REBELinBLUE\Deployer\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use MicheleAngioni\MultiLanguage\Exceptions\LanguageNotFoundException;
use MicheleAngioni\MultiLanguage\LanguageManager;
use Symfony\Component\HttpFoundation\Response;

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
     * @param Request     $request
     * @param Closure     $next
     * @param string|null $guard
     *
     * @throws LanguageNotFoundException
     * @return RedirectResponse|Response
     */
    public function handle(Request $request, Closure $next, ?string $guard = null)
    {
        $user = $this->auth->guard($guard)->user();
        if ($user && $user->language) {
            $this->languageManager->setLocale($user->language);
        }

        return $next($request);
    }
}
