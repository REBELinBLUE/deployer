<?php

namespace REBELinBLUE\Deployer\Tests\Unit\View\Composers;

use Illuminate\Contracts\View\View;
use Mockery as m;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\GroupRepositoryInterface;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Composers\NavigationComposer;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\View\Composers\NavigationComposer
 */
class NavigationComposerTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::compose
     */
    public function testComposeWithProject()
    {
        $active_group   = 1;
        $active_project = 2;
        $items          = ['pending 1', 'pending 2', 'pending 3'];

        $project           = new Project();
        $project->group_id = $active_group;
        $project->id       = $active_project;

        $view          = m::mock(View::class);
        $view->project = $project;

        $view->shouldReceive('with')->once()->with('active_group', $active_group);
        $view->shouldReceive('with')->once()->with('active_project', $active_project);
        $view->shouldReceive('with')->once()->with('groups', $items);

        $repository = m::mock(GroupRepositoryInterface::class);
        $repository->shouldReceive('getAll')->andReturn($items);

        $composer = new NavigationComposer($repository);
        $composer->compose($view);
    }

    /**
     * @covers ::__construct
     * @covers ::compose
     */
    public function testComposeWithoutProject()
    {
        $groups = [];

        $view = m::mock(View::class);

        $view->shouldReceive('with')->once()->with('active_group', null);
        $view->shouldReceive('with')->once()->with('active_project', null);
        $view->shouldReceive('with')->once()->with('groups', $groups);

        $repository = m::mock(GroupRepositoryInterface::class);
        $repository->shouldReceive('getAll')->andReturn($groups);

        $composer = new NavigationComposer($repository);
        $composer->compose($view);
    }
}
