<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Providers;

use Illuminate\Foundation\Application;
use Mockery as m;
use REBELinBLUE\Deployer\Providers\RepositoryServiceProvider;
use REBELinBLUE\Deployer\Tests\TestCase;
use ReflectionClass;

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
        $app = m::mock(Application::class);
        $app->shouldReceive('bind')
            ->times(17)
            ->with(m::type('string'), m::type('string'))
            ->andReturnUsing(function ($interface, $repository) {
                $mock = new ReflectionClass($interface);
                $instance = m::mock($repository);

                $this->assertTrue($mock->isInterface());
                $this->assertInstanceOf($interface, $instance);
            }); // FIXME: Change this to withArgs when using mockery 1.0

        $provider = new RepositoryServiceProvider($app);
        $provider->register();
    }
}
