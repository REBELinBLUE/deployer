<?php

namespace REBELinBLUE\Deployer\Tests\View\Composers;

use Illuminate\Contracts\View\View;
use Mockery;
use REBELinBLUE\Deployer\Repositories\Contracts\GroupRepositoryInterface;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Composers\NavigationComposer;

class NavigationComposerTest extends TestCase
{
    public function testCompose()
    {
        $active_group   = 1;
        $active_project = 2;
        $items          = ['pending 1', 'pending 2', 'pending 3'];

        $view = Mockery::mock(View::class);

        // TODO: Check if this is the best value to do it
        $view->project = (object) [
            'group_id'    => $active_group,
            'id'          => $active_project,
            'is_template' => false,
        ];

        $view->shouldReceive('with')->once()->with('active_group', $active_group);
        $view->shouldReceive('with')->once()->with('active_project', $active_project);
        $view->shouldReceive('with')->once()->with('groups', $items);

        $repository = Mockery::mock(GroupRepositoryInterface::class);
        $repository->shouldReceive('getAll')->andReturn($items);

        $composer = new NavigationComposer($repository);
        $composer->compose($view);
    }
}
