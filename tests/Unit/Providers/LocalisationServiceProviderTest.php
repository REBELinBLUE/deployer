<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Providers;

use Illuminate\Contracts\Foundation\Application;
use JsLocalization\Caching\ConfigCachingService;
use JsLocalization\Caching\MessageCachingService;
use JsLocalization\Utils\Helper;
use MicheleAngioni\MultiLanguage\LanguageManager;
use Mockery as m;
use REBELinBLUE\Deployer\Providers\LocalisationServiceProvider;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Providers\LocalisationServiceProvider
 */
class LocalisationServiceProviderTest extends TestCase
{
    /**
     * @covers ::register
     */
    public function testRegister()
    {
        $app = m::mock(Application::class);
        $app->shouldReceive('singleton')->with('locale', m::type('closure'));
        $app->shouldReceive('singleton')->with('JsLocalizationMessageCachingService', m::type('closure'));
        $app->shouldReceive('singleton')->with('JsLocalizationHelper', m::type('closure'));
        $app->shouldReceive('singleton')->with('JsLocalizationConfigCachingService', m::type('closure'));

        $provider = new LocalisationServiceProvider($app);
        $provider->register();
    }

    /**
     * @covers ::register
     */
    public function testRegisterIsExpectedTypes()
    {
        $this->assertInstanceOf(LanguageManager::class, $this->app->make('locale'));
        $this->assertInstanceOf(MessageCachingService::class, $this->app->make('JsLocalizationMessageCachingService'));
        $this->assertInstanceOf(Helper::class, $this->app->make('JsLocalizationHelper'));
        $this->assertInstanceOf(ConfigCachingService::class, $this->app->make('JsLocalizationConfigCachingService'));
    }
}
