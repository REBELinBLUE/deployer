<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Providers;

use Illuminate\Foundation\Application;
use Mockery as m;
use REBELinBLUE\Deployer\Providers\RepositoryServiceProvider;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Providers\RepositoryServiceProvider
 */
class RepositoryServiceProviderTest extends TestCase
{
    /**
     * @xcover ::register
     */
    public function testRegister()
    {
        $app = m::mock(Application::class); // TODO: Improve this
        $app->shouldReceive('bind')->zeroOrMoreTimes()->with(m::type('string'), m::type('string'));

        $provider = new RepositoryServiceProvider($app);
        $provider->register();
    }
}
