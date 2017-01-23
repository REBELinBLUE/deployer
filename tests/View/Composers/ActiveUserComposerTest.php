<?php

namespace REBELinBLUE\Deployer\Tests\View\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Composers\ActiveUserComposer;

class ActiveUserComposerTest extends TestCase
{
    public function testCompose()
    {
        $expected_user = 123456;

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($expected_user);

        $view = $this->getMockBuilder(View::class)
                     ->disableOriginalConstructor()
                     ->getMock();

        $view->expects($this->any())
             ->method('with')
             ->withConsecutive(
                 [$this->equalTo('logged_in_user'), $this->equalTo($expected_user)]
             );

        $composer = new ActiveUserComposer;
        $composer->compose($view);
    }
}
