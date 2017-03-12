<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Http\Middleware;

use Illuminate\Contracts\Auth\Factory;
use Illuminate\Http\Request;
use MicheleAngioni\MultiLanguage\LanguageManager;
use Mockery as m;
use REBELinBLUE\Deployer\Http\Middleware\Locale;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Middleware\Locale
 */
class LocaleTest extends TestCase
{
    private $auth;
    private $manager;

    public function setUp()
    {
        parent::setUp();

        $this->auth    = m::mock(Factory::class);
        $this->manager = m::mock(LanguageManager::class);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleNotAuthenticated()
    {
        $expectedGuard = 'session';

        $expected = m::mock(Request::class);

        $this->manager->shouldNotReceive('setLocale');

        $closure  = function ($request) use ($expected) {
            $this->assertSame($request, $expected);

            return true;
        };

        $this->auth->shouldReceive('guard')->with($expectedGuard)->andReturnSelf();
        $this->auth->shouldReceive('user')->andReturn(null);

        $middleware = new Locale($this->manager, $this->auth);
        $actual     = $middleware->handle($expected, $closure, $expectedGuard);

        $this->assertTrue($actual);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleWhenAuthenticatedWithoutLanguageSet()
    {
        $expectedGuard = 'session';

        $expected = m::mock(Request::class);

        $this->manager->shouldNotReceive('setLocale');

        $closure  = function ($request) use ($expected) {
            $this->assertSame($request, $expected);

            return true;
        };

        $this->auth->shouldReceive('guard')->with($expectedGuard)->andReturnSelf();
        $this->auth->shouldReceive('user')->andReturn((object) ['language' => null]);

        $middleware = new Locale($this->manager, $this->auth);
        $actual     = $middleware->handle($expected, $closure, $expectedGuard);

        $this->assertTrue($actual);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleWhenAuthenticatedWithLanguageSet()
    {
        $expectedGuard    = 'session';
        $expectedLanguage = 'en';

        $expected = m::mock(Request::class);

        $this->manager->shouldReceive('setLocale')->with($expectedLanguage);

        $closure  = function ($request) use ($expected) {
            $this->assertSame($request, $expected);

            return true;
        };

        $this->auth->shouldReceive('guard')->with($expectedGuard)->andReturnSelf();
        $this->auth->shouldReceive('user')->andReturn((object) ['language' => $expectedLanguage]);

        $middleware = new Locale($this->manager, $this->auth);
        $actual     = $middleware->handle($expected, $closure, $expectedGuard);

        $this->assertTrue($actual);
    }
}
