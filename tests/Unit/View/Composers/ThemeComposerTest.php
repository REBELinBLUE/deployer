<?php

namespace REBELinBLUE\Deployer\Tests\Unit\View\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Mockery as m;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Composers\ThemeComposer;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\View\Composers\ThemeComposer
 */
class ThemeComposerTest extends TestCase
{
    /**
     * @covers ::compose
     */
    public function testComposeIncludesDefaultTheme()
    {
        $expected_theme = config('deployer.theme');

        $view = m::mock(View::class);
        $view->shouldReceive('with')->once()->with('theme', $expected_theme);

        $composer = new ThemeComposer();
        $composer->compose($view);
    }

    /**
     * @covers ::compose
     */
    public function testComposeIncludesUserTheme()
    {
        Auth::shouldReceive('user')->once()->andReturn((object) ['skin' => 'pink']);

        $view = m::mock(View::class);
        $view->shouldReceive('with')->once()->with('theme', 'pink');

        $composer = new ThemeComposer();
        $composer->compose($view);
    }
}
