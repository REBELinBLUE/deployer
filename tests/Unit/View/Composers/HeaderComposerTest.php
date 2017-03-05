<?php

namespace REBELinBLUE\Deployer\Tests\Unit\View\Composers;

use Illuminate\Contracts\View\View;
use Mockery as m;
use REBELinBLUE\Deployer\Repositories\Contracts\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Composers\HeaderComposer;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\View\Composers\HeaderComposer
 */
class HeaderComposerTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::compose
     */
    public function testCompose()
    {
        $items = ['pending 1', 'pending 2', 'pending 3'];

        $view = m::mock(View::class);
        $view->shouldReceive('with')->once()->with('pending', $items);
        $view->shouldReceive('with')->once()->with('pending_count', 3);
        $view->shouldReceive('with')->once()->with('deploying', $items);
        $view->shouldReceive('with')->once()->with('deploying_count', 3);

        $repository = m::mock(DeploymentRepositoryInterface::class);
        $repository->shouldReceive('getPending')->once()->andReturn($items);
        $repository->shouldReceive('getRunning')->once()->andReturn($items);

        $composer = new HeaderComposer($repository);
        $composer->compose($view);
    }
}
