<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Providers;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Mockery as m;
use REBELinBLUE\Deployer\Providers\ViewServiceProvider;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Composers\ViewComposerInterface;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Providers\ViewServiceProvider
 */
class ViewServiceProviderTest extends TestCase
{
    /**
     * @xcover ::boot
     */
    public function testBoot()
    {
        $app     = m::mock(Application::class);
        $factory = m::mock(ViewFactory::class);

        $factory->shouldReceive('composer')
                ->times(5)
                ->with(m::type('array'), m::on(function ($composer) {
                    $instance = m::mock($composer);
                    $this->assertInstanceOf(ViewComposerInterface::class, $instance);

                    return ($instance instanceof ViewComposerInterface);
                }));

        $provider = new ViewServiceProvider($app);
        $provider->boot($factory);
    }
}
