<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Providers;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Log\Writer;
use Mockery as m;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\stubs\LogServiceProvider;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Providers\LogServiceProvider
 */
class LogServiceProviderTest extends TestCase
{
    /**
     * @covers ::configureSingleHandler
     * @covers ::getFileName
     */
    public function testConfigureSingleHandler()
    {
        $name  = php_sapi_name();
        $level = 'debug';

        $config = m::mock(Config::class);
        $config->shouldReceive('get')->once()->with('app.log_level', 'debug')->andReturn($level);

        $app = m::mock(Application::class);
        $app->shouldReceive('storagePath')->once()->andReturn('/tmp');
        $app->shouldReceive('bound')->once()->with('config')->andReturn(true);
        $app->shouldReceive('make')->once()->with('config')->andReturn($config);

        $writer = m::mock(Writer::class);
        $writer->shouldReceive('useFiles')->once()->with('/tmp/logs/' . $name . '.log', $level);

        $log = new LogServiceProvider($app);
        $log->configureSingleHandler($writer);
    }

    /**
     * @covers ::configureDailyHandler
     * @covers ::getFileName
     */
    public function testConfigureDailyHandler()
    {
        $name  = php_sapi_name();
        $level = 'debug';
        $days  = 10;

        $config = m::mock(Config::class);
        $config->shouldReceive('get')->once()->with('app.log_max_files', 5)->andReturn($days);
        $config->shouldReceive('get')->once()->with('app.log_level', 'debug')->andReturn($level);

        $app = m::mock(Application::class);
        $app->shouldReceive('bound')->atLeast()->once()->with('config')->andReturn(true);
        $app->shouldReceive('make')->atLeast()->once()->with('config')->andReturn($config);
        $app->shouldReceive('storagePath')->once()->andReturn('/tmp');

        $writer = m::mock(Writer::class);
        $writer->shouldReceive('useDailyFiles')->once()->with('/tmp/logs/' . $name . '.log', $days, $level);

        $log = new LogServiceProvider($app);
        $log->configureDailyHandler($writer);
    }
}
