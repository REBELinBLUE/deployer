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
                 [$this->equalTo('theme'), $this->equalTo($expected_theme)]
             );

        $composer = new ThemeComposer;
        $composer->compose($view);
    }
}
