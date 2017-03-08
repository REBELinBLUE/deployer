<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Providers;

use Illuminate\Foundation\Application;
use Mockery as m;
use REBELinBLUE\Deployer\Jobs\QueueDeployment\StepsBuilder;
use REBELinBLUE\Deployer\Providers\ProcessServiceProvider;
use REBELinBLUE\Deployer\Services\Scripts\Parser;
use REBELinBLUE\Deployer\Services\Scripts\Runner;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Providers\ProcessServiceProvider
 */
class ProcessServiceProviderTest extends TestCase
{
    /**
     * @covers ::register
     */
    public function testRegister()
    {
        $app = m::mock(Application::class);
        $app->shouldReceive('bind')->with(StepsBuilder::class, m::type('closure'));
        $app->shouldReceive('bind')->with(Parser::class, m::type('closure'));
        $app->shouldReceive('bind')->with(Runner::class, m::type('closure'));

        $provider = new ProcessServiceProvider($app);
        $provider->register();
    }

    /**
     * @covers ::register
     */
    public function testRegisterIsExpectedTypes()
    {
        $this->assertInstanceOf(StepsBuilder::class, $this->app->make(StepsBuilder::class));
        $this->assertInstanceOf(Parser::class, $this->app->make(Parser::class));
        $this->assertInstanceOf(Runner::class, $this->app->make(Runner::class));
    }
}
