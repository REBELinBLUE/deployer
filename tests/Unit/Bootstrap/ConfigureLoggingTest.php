<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Bootstrap;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Log\Writer;
use Mockery as m;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Bootstrap\Stubs\ConfigureLogging;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Bootstrap\ConfigureLogging
 */
class ConfigureLoggingTest extends TestCase
{
    /**
     * @covers ::configureSingleHandler
     */
    public function testConfigureSingleHandler()
    {
        $name  = php_sapi_name();
        $level = 'debug';

        $config = m::mock(Config::class);
        $config->shouldReceive('get')->once()->with('app.log_level', 'debug')->andReturn($level);

        $app = m::mock(Application::class);
        $app->shouldReceive('storagePath')->once()->andReturn('/tmp');
        $app->shouldReceive('make')->once()->with('config')->andReturn($config);

        $writer = m::mock(Writer::class);
        $writer->shouldReceive('useFiles')->once()->shouldReceive('/tmp/logs/' . $name . 'log', $level);

        $log = new ConfigureLogging();
        $log->configureSingleHandler($app, $writer);
    }

    /**
     * @covers ::configureDailyHandler
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
        $app->shouldReceive('storagePath')->once()->andReturn('/tmp');
        $app->shouldReceive('make')->once()->with('config')->andReturn($config);

        $writer = m::mock(Writer::class);
        $writer->shouldReceive('useDailyFiles')->once()->shouldReceive('/tmp/logs/' . $name . 'log', $days, $level);

        $log = new ConfigureLogging();
        $log->configureDailyHandler($app, $writer);
    }
}
