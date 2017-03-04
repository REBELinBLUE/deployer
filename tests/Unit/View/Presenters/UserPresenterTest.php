<?php

namespace REBELinBLUE\Deployer\Tests\Unit\View\Presenters;

use Creativeorange\Gravatar\Gravatar;
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
     * @covers ::__construct
     * @covers ::presentAvatarUrl
     */
    public function testPresentAvatarUrlReturnsUploadedAvatar()
    {
        $expected = 'image.jpg';

        $user = m::mock(User::class);
        $user->shouldReceive('getAttribute')->atLeast()->once()->with('avatar')->andReturn($expected);

        $gravatar = m::mock(Gravatar::class);
        $gravatar->shouldNotReceive('get');

        $presenter = new UserPresenter($gravatar);
        $presenter->setWrappedObject($user);
        $actual    = $presenter->avatar_url;

        $this->assertSame(config('app.url') . '/' . $expected, $actual);
    }

    /**
     * @covers ::__construct
     * @covers ::presentAvatarUrl
     */
    public function testPresentAvatarDefaultsToGravatar()
    {
        $email    = 'user@example.com';
        $expected = 'a-gravatar-url';

        $gravatar = m::mock(Gravatar::class);
        $gravatar->shouldReceive('get')->once()->with($email)->andReturn($expected);

        $user = m::mock(User::class);
        $user->shouldReceive('getAttribute')->once()->with('avatar')->andReturn(false);
        $user->shouldReceive('getAttribute')->atLeast()->once()->with('email')->andReturn($email);

        $presenter = new UserPresenter($gravatar);
        $presenter->setWrappedObject($user);
        $actual    = $presenter->avatar_url;

        $this->assertSame($expected, $actual);
    }
}
