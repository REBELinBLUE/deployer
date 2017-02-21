<?php

namespace REBELinBLUE\Deployer\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
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
     * Locale constructor.
     * @param LanguageManager $languageManager
     */
    public function __construct(LanguageManager $languageManager)
    {
        $this->languageManager = $languageManager;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($user && $user->language) {
            $this->languageManager->setLocale($user->language);
        }

        return $next($request);
    }
}
