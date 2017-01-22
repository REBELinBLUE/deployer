<?php

use REBELinBLUE\Deployer\View\Composers\NavigationComposer;
use Illuminate\Contracts\View\View;
use REBELinBLUE\Deployer\Contracts\Repositories\GroupRepositoryInterface;

class NavigationComposerTest extends TestCase
{
    public function testCompose()
    {
        $active_group = 1;
        $active_project = 2;
        $items = ['pending 1', 'pending 2', 'pending 3'];

        $view = $this->getMockBuilder(View::class)
                     ->disableOriginalConstructor()
                     ->getMock();

        // TODO: Check if this is the best value to do it
        $view->project = (object) [
            'group_id'    => $active_group,
            'id'          => $active_project,
            'is_template' => false
        ];

        $view->expects($this->any())
             ->method('with')
             ->withConsecutive(
                 [$this->equalTo('active_group'), $this->equalTo($active_group)],
                 [$this->equalTo('active_project'), $this->equalTo($active_project)],
                 [$this->equalTo('groups'), $this->equalTo($items)]
             );

        $repository = $this->getMockBuilder(GroupRepositoryInterface::class)
                           ->disableOriginalConstructor()
                           ->getMock();

        $repository->expects($this->once())
                   ->method('getAll')
                   ->willReturn($items);

        $composer = new NavigationComposer($repository);
        $composer->compose($view);
    }
}
