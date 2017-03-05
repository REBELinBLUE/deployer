<?php

namespace REBELinBLUE\Deployer\Tests\Unit\View\Composers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\View;
use Mockery as m;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Composers\ActiveUserComposer;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\View\Composers\ActiveUserComposer
 */
class ActiveUserComposerTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::compose
     */
    public function testCompose()
    {
        $expected_user = 123456;

        $view = m::mock(View::class);
        $view->shouldReceive('with')->once()->with('logged_in_user', $expected_user);

        $auth = m::mock(Guard::class);
        $auth->shouldReceive('user')->once()->andReturn($expected_user);

        $composer = new ActiveUserComposer($auth);
        $composer->compose($view);
    }
}
