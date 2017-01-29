<?php

namespace REBELinBLUE\Deployer\Tests\Unit\View\Presenters;

use Creativeorange\Gravatar\Facades\Gravatar;
use Mockery as m;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;
use REBELinBLUE\Deployer\View\Presenters\UserPresenter;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\View\Presenters\UserPresenter
 */
class UserPresenterTest extends TestCase
{
    /**
     * @covers ::presentAvatarUrl
     */
    public function testPresentAvatarUrlReturnsUploadedAvatar()
    {
        $expected = 'image.jpg';

        $user = m::mock(User::class);
        $user->shouldReceive('getAttribute')->atLeast()->times(1)->with('avatar')->andReturn($expected);

        $presenter = new UserPresenter($user);
        $actual    = $presenter->presentAvatarUrl();

        $this->assertSame($this->baseUrl . '/' . $expected, $actual);
    }

    /**
     * @covers ::presentAvatarUrl
     */
    public function testPresentAvatarDefaultsToGravatar()
    {
        $email    = 'user@example.com';
        $expected = 'a-gravatar-url';

        $user = m::mock(User::class);
        $user->shouldReceive('getAttribute')->once()->with('avatar')->andReturn(false);
        $user->shouldReceive('getAttribute')->atLeast()->times(1)->with('email')->andReturn($email);

        Gravatar::shouldReceive('get')->once()->with($email)->andReturn($expected);

        $presenter = new UserPresenter($user);
        $actual    = $presenter->presentAvatarUrl();

        $this->assertSame($expected, $actual);
    }
}
