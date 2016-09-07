<?php

use REBELinBLUE\Deployer\Composers\ActiveUserComposer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ActiveUserComposerTest extends TestCase
{
    public function testCompose()
    {
        $this->markTestSkipped('broken');

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
