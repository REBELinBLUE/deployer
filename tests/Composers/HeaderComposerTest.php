<?php

use REBELinBLUE\Deployer\View\Composers\HeaderComposer;
use Illuminate\Contracts\View\View;
use REBELinBLUE\Deployer\Contracts\Repositories\DeploymentRepositoryInterface;

class HeaderComposerTest extends TestCase
{
    public function testCompose()
    {
        $items = ['pending 1', 'pending 2', 'pending 3'];

        $view = $this->getMockBuilder(View::class)
                     ->disableOriginalConstructor()
                     ->getMock();

        $view->expects($this->any())
             ->method('with')
             ->withConsecutive(
                 [$this->equalTo('pending'), $this->equalTo($items)],
                 [$this->equalTo('pending_count'), $this->equalTo(3)],
                 [$this->equalTo('deploying'), $this->equalTo($items)],
                 [$this->equalTo('deploying_count'), $this->equalTo(3)]
             );

        $repository = $this->getMockBuilder(DeploymentRepositoryInterface::class)
                           ->disableOriginalConstructor()
                           ->getMock();

        $repository->expects($this->once())
                   ->method('getPending')
                   ->willReturn($items);

        $repository->expects($this->once())
                   ->method('getRunning')
                   ->willReturn($items);

        $composer = new HeaderComposer($repository);
        $composer->compose($view);
    }
}
