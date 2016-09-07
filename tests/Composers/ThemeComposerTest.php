<?php

use REBELinBLUE\Deployer\Composers\ThemeComposer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ThemeComposerTest extends TestCase
{
    public function testCompose()
    {
        $expected_theme = config('deployer.theme');

        $view = $this->getMockBuilder(View::class)
                     ->disableOriginalConstructor()
                     ->getMock();

        $view->expects($this->any())
             ->method('with')
             ->withConsecutive(
                 [$this->equalTo('body'), $this->equalTo('login-page')],
                 [$this->equalTo('body'), $this->equalTo('skin-' . $expected_theme)],
                 [$this->equalTo('body'), $this->equalTo('skin-pink')]
             );

        $composer = new ThemeComposer;
        $composer->compose($view);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn((object) ['skin' => null]);

        $composer->compose($view);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn((object) ['skin' => 'pink']);

        $composer->compose($view);
    }
}
