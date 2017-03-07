<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Providers;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application;
use Mockery as m;
use REBELinBLUE\Deployer\Providers\WhoopsServiceProvider;
use REBELinBLUE\Deployer\Tests\TestCase;
use Whoops\Run as Whoops;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Providers\WhoopsServiceProvider
 * @todp Test that the correct type of response is returned
 */
class WhoopsServiceProviderTest extends TestCase
{
    /**
     * @covers ::register
     * @covers ::useWhoops
     */
    public function testRegisterShouldRegisterWhenDebugIsEnabled()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->once()->with('app.debug', false)->andReturn(true);

        $app = m::mock(Application::class);
        $app->shouldReceive('make')->once()->with('config')->andReturn($config);
        $app->shouldReceive('bind')->once()->with(Whoops::class, m::type('callable'));

        $whoops = new WhoopsServiceProvider($app);
        $actual = $whoops->register();

        $this->assertTrue($actual);
    }

    /**
     * @covers ::register
     * @covers ::useWhoops
     */
    public function testRegisterShouldNotRegisterWhenDebugIsDisabled()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->once()->with('app.debug', false)->andReturn(false);

        $app = m::mock(Application::class);
        $app->shouldReceive('make')->once()->with('config')->andReturn($config);

        $whoops = new WhoopsServiceProvider($app);
        $actual = $whoops->register();

        $this->assertFalse($actual);
    }

    /**
     * @covers ::provides
     * @covers ::useWhoops
     */
    public function testProvidesShouldReturnExpectedArrayWhenDebugIsEnabled()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->once()->with('app.debug', false)->andReturn(true);

        $app = m::mock(Application::class);
        $app->shouldReceive('make')->once()->with('config')->andReturn($config);

        $whoops = new WhoopsServiceProvider($app);
        $actual = $whoops->provides();

        $this->assertSame([Whoops::class], $actual);
    }

    /**
     * @covers ::provides
     * @covers ::useWhoops
     */
    public function testProvidesShouldReturnEmptyArrayWhenDebugIsDisabled()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->once()->with('app.debug', false)->andReturn(false);

        $app = m::mock(Application::class);
        $app->shouldReceive('make')->once()->with('config')->andReturn($config);
        $app->shouldNotReceive('bind')->with(Whoops::class, m::type('callable'));

        $whoops = new WhoopsServiceProvider($app);
        $actual = $whoops->provides();

        $this->assertSame([], $actual);
    }

    /**
     * @covers ::register
     */
    public function testRegisterIsExpectedTypes()
    {
        $this->assertInstanceOf(Whoops::class, $this->app->make(Whoops::class));
    }
}
