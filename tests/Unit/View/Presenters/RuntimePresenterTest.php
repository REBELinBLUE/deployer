<?php

namespace REBELinBLUE\Deployer\Tests\Unit\View\Presenters;

use Illuminate\Support\Facades\Lang;
use Mockery as m;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\View\Presenters\Stubs\StubModel;
use REBELinBLUE\Deployer\Tests\Unit\View\Presenters\Stubs\StubPresenter;
use RuntimeException;
use stdClass;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\View\Presenters\RuntimePresenter
 */
class RuntimePresenterTest extends TestCase
{
    const SECOND = 1;
    const MINUTE = 60;
    const HOUR   = 3600;

    /**
     * @covers ::presentReadableRuntime
     */
    public function testRuntimeInterfaceIsUsed()
    {
        $this->expectException(RuntimeException::class);

        // Class which doesn't implement the RuntimeInterface
        $presenter = new StubPresenter(new stdClass());
        $presenter->presentReadableRuntime();
    }

    /**
     * @dataProvider getRuntimeLabels
     * @covers ::presentReadableRuntime
     */
    public function testReadableRuntimeIsFormatted($translations, $runtime)
    {
        $expected = implode(', ', $translations); // minute; hour, minute; minute, second; etc

        $model = m::mock(StubModel::class);
        $model->shouldReceive('runtime')->once()->andReturn($runtime);

        foreach ($translations as $key) {
            $time        = $runtime === 0 ? 0 : 1;
            $translation = 'deployments.' . $key;

            Lang::shouldReceive('choice')->once()->with($translation, $time, ['time' => $time])->andReturn($key);
        }

        $presenter = new StubPresenter($model);
        $actual    = $presenter->presentReadableRuntime();

        $this->assertSame($expected, $actual);
    }

    public function getRuntimeLabels()
    {
        return [
            [['second'],                   0],
            [['second'],                   self::SECOND],
            [['minute'],                   self::MINUTE],
            [['minute', 'second'],         self::MINUTE + self::SECOND],
            [['hour'],                     self::HOUR],
            [['hour', 'second'],           self::HOUR + self::SECOND],
            [['hour', 'minute'],           self::HOUR + self::MINUTE],
            [['hour', 'minute', 'second'], self::HOUR + self::MINUTE + self::SECOND],
        ];
    }

    /**
     * @dataProvider getVeryLongRuntimes
     * @covers ::presentReadableRuntime
     */
    public function testReadableRuntimeFormatsLongRuntime($runtime)
    {
        $expected = 'deployments.very_long_time';

        $model = m::mock(StubModel::class);
        $model->shouldReceive('runtime')->once()->andReturn($runtime);

        Lang::shouldReceive('get')->once()->with($expected)->andReturn($expected);

        $presenter = new StubPresenter($model);
        $actual    = $presenter->presentReadableRuntime();

        $this->assertSame($expected, $actual);
    }

    public function getVeryLongRuntimes()
    {
        return [              // The check is for 3 or more hours so
            [self::HOUR * 3], // make sure == works
            [self::HOUR * 6],  // and > works
        ];
    }
}
