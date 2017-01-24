<?php

namespace REBELinBLUE\Deployer\Tests\View\Presenters;

use Creativeorange\Gravatar\Facades\Gravatar;
use Mockery as m;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;
use REBELinBLUE\Deployer\View\Presenters\UserPresenter;

class UserPresenterTest extends TestCase
{
    public function testPresentAvatarUrlReturnsUploadedAvatar()
    {
        $expected = 'image.jpg';

        $user = m::mock(User::class);
        $user->shouldReceive('getAttribute')->with('avatar')->andReturn($expected);

        $presenter = new UserPresenter($user);
        $actual    = $presenter->presentAvatarUrl();

        $this->assertEquals($this->baseUrl . '/' . $expected, $actual);
    }

    public function testPresentAvatarDefaultsToGravatar()
    {
        $email    = 'user@example.com';
        $expected = 'a-gravatar-url';

        $user = m::mock(User::class);
        $user->shouldReceive('getAttribute')->with('avatar')->andReturn(false);
        $user->shouldReceive('getAttribute')->with('email')->andReturn($email);

        Gravatar::shouldReceive('get')->with($email)->andReturn($expected);

        $presenter = new UserPresenter($user);
        $actual    = $presenter->presentAvatarUrl();

        $this->assertEquals($expected, $actual);
    }
}
