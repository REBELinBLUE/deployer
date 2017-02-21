<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use JsLocalization\Caching\ConfigCachingService;
use JsLocalization\Caching\MessageCachingService;
use JsLocalization\Utils\Helper;
use MicheleAngioni\MultiLanguage\LanguageManager;
use Themsaid\Langman\LangmanServiceProvider;

/**
 * Service provider for localisation related packages.
 */
class LocalisationServiceProvider extends ServiceProvider
{
    /**
     * Register the dependencies for andywer/js-localization. Done like this instead of using the packages
     * provided service provider as we don't want the routes included.
     */
    public function register()
    {
        $this->app->singleton('locale', function (Application $app) {
            return $app->make(LanguageManager::class);
        });

        $this->app->singleton('JsLocalizationMessageCachingService', function () {
            return new MessageCachingService();
        });

        $this->app->singleton('JsLocalizationHelper', function () {
            return new Helper();
        });

        $this->app->singleton('JsLocalizationConfigCachingService', function () {
            return new ConfigCachingService();
        });

        if ($this->app->environment('local', 'testing') && class_exists(LangmanServiceProvider::class, true)) {
            $this->app->register(LangmanServiceProvider::class);
        }
    }
}
