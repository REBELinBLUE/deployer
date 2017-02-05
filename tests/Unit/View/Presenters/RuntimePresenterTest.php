<?php

namespace REBELinBLUE\Deployer\Tests\Unit\View\Presenters;

use Illuminate\Support\Facades\Lang;
use Mockery as m;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\stubs\Model as StubModel;
use REBELinBLUE\Deployer\Tests\Unit\stubs\Presenter as StubPresenter;
use RuntimeException;
use stdClass;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\View\Presenters\RuntimePresenter
 */
class RuntimePresenterTest extends TestCase
{
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
     * @dataProvider provideRuntimeLabels
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

    public function provideRuntimeLabels()
    {
        return $this->fixture('View/Presenters/RuntimePresenter')['runtimes'];
    }

    /**
     * @dataProvider provideVeryLongRuntimes
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

    public function provideVeryLongRuntimes()
    {
        return $this->fixture('View/Presenters/RuntimePresenter')['long'];
    }
}
