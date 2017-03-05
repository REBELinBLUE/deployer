<?php

namespace REBELinBLUE\Deployer\Tests\Unit\View\Composers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\View;
use Mockery as m;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Composers\ThemeComposer;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\View\Composers\ThemeComposer
 */
class ThemeComposerTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::compose
     */
    public function testComposeIncludesDefaultTheme()
    {
        $expected_theme = config('deployer.theme');

        $view = m::mock(View::class);
        $view->shouldReceive('with')->once()->with('theme', $expected_theme);

        $auth = m::mock(Guard::class);
        $auth->shouldReceive('user')->andReturnNull();

        $composer = new ThemeComposer($auth);
        $composer->compose($view);
    }

    /**
     * @covers ::__construct
     * @covers ::compose
     */
    public function testComposeIncludesUserTheme()
    {
        $auth = m::mock(Guard::class);
        $auth->shouldReceive('user')->once()->andReturn((object) ['skin' => 'pink']);

        $view = m::mock(View::class);
        $view->shouldReceive('with')->once()->with('theme', 'pink');

        $composer = new ThemeComposer($auth);
        $composer->compose($view);
    }
}
