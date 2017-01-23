<?php

namespace REBELinBLUE\Deployer\Tests\View\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Mockery;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Composers\ThemeComposer;

class ThemeComposerTest extends TestCase
{
    public function testComposeIncludesDefaultTheme()
    {
        $expected_theme = config('deployer.theme');

        $view = Mockery::mock(View::class);
        $view->shouldReceive('with')->once()->with('theme', $expected_theme);

        $composer = new ThemeComposer;
        $composer->compose($view);
    }

    public function testComposeIncludesUserTheme()
    {
        Auth::shouldReceive('user')->once()->andReturn((object) ['skin' => 'pink']);

        $view = Mockery::mock(View::class);
        $view->shouldReceive('with')->once()->with('theme', 'pink');

        $composer = new ThemeComposer;
        $composer->compose($view);
    }
}
