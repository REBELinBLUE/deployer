<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Providers;

use Mockery as m;
use REBELinBLUE\Deployer\Providers\HelperServiceProvider;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Providers\HelperServiceProvider
 */
class HelperServiceProviderTest extends TestCase
{
    /**
     * @covers ::boot
     */
    public function testBoot()
    {
        $helpers = ['helper.php', 'helper2.php'];
        $glob    = app_path('Helpers') . '/*Helper.php';

        $filesystem = m::mock(Filesystem::class);
        $filesystem->shouldReceive('glob')->once()->with($glob)->andReturn($helpers);

        $filesystem->shouldReceive('requireOnce')->once()->with('helper.php');
        $filesystem->shouldReceive('requireOnce')->once()->with('helper2.php');

        $this->app->instance('files', $filesystem);

        $provider = new HelperServiceProvider($this->app);
        $provider->boot();
    }
}
