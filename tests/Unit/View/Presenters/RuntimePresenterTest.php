<?php

namespace REBELinBLUE\Deployer\Tests\Unit\View\Presenters;

use Illuminate\Contracts\Translation\Translator;
use Mockery as m;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\stubs\Model as StubModel;
use REBELinBLUE\Deployer\Tests\Unit\stubs\Presenter as StubPresenter;
use REBELinBLUE\Deployer\User;
use RuntimeException;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\View\Presenters\RuntimePresenter
 */
class RuntimePresenterTest extends TestCase
{
    private $translator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = m::mock(Translator::class);
    }

    /**
     * @covers ::presentReadableRuntime
     */
    public function testRuntimeInterfaceIsUsed()
    {
        $this->expectException(RuntimeException::class);

        $invalid = new User();

        // Class which doesn't implement the RuntimeInterface
        $presenter = new StubPresenter($this->translator);
        $presenter->setWrappedObject($invalid);
        $presenter->presentReadableRuntime();
    }

    /**
     * @dataProvider provideRuntimeLabels
     * @covers ::presentReadableRuntime
     *
     * @param array $translations
     * @param int   $runtime
     */
    public function testReadableRuntimeIsFormatted(array $translations, int $runtime)
    {
        $expected = implode(', ', $translations); // minute; hour, minute; minute, second; etc

        $model = m::mock(StubModel::class);
        $model->shouldReceive('runtime')->once()->andReturn($runtime);

        foreach ($translations as $key) {
            $time        = $runtime === 0 ? 0 : 1;
            $translation = 'deployments.' . $key;

            $this->translator->shouldReceive('choice')
                             ->once()
                             ->with($translation, $time, ['time' => $time])
                             ->andReturn($key);
        }

        $presenter = new StubPresenter($this->translator);
        $presenter->setWrappedObject($model);
        $actual    = $presenter->presentReadableRuntime();

        $this->assertSame($expected, $actual);
    }

    public function provideRuntimeLabels(): array
    {
        return $this->fixture('View/Presenters/RuntimePresenter')['runtimes'];
    }

    /**
     * @dataProvider provideVeryLongRuntimes
     * @covers ::presentReadableRuntime
     *
     * @param int $runtime
     */
    public function testReadableRuntimeFormatsLongRuntime(int $runtime)
    {
        $expected = 'deployments.very_long_time';

        $model = m::mock(StubModel::class);
        $model->shouldReceive('runtime')->once()->andReturn($runtime);

        $this->translator->shouldReceive('get')->once()->with($expected)->andReturn($expected);

        $presenter = new StubPresenter($this->translator);
        $presenter->setWrappedObject($model);
        $actual    = $presenter->presentReadableRuntime();

        $this->assertSame($expected, $actual);
    }

    public function provideVeryLongRuntimes(): array
    {
        return $this->fixture('View/Presenters/RuntimePresenter')['long'];
    }
}
