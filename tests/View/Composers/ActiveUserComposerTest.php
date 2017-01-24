<?php

namespace REBELinBLUE\Deployer\Tests\View\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Mockery as m;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Composers\ActiveUserComposer;

class ActiveUserComposerTest extends TestCase
{
    public function testCompose()
    {
        $expected_user = 123456;

        Auth::shouldReceive('user')->once()->andReturn($expected_user);

        $view = m::mock(View::class);
        $view->shouldReceive('with')->once()->with('logged_in_user', $expected_user);

        $composer = new ActiveUserComposer;
        $composer->compose($view);
    }
}
