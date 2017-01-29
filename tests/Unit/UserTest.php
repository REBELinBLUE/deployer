<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Notification;
use REBELinBLUE\Deployer\Notifications\System\ResetPassword;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;
use REBELinBLUE\Deployer\View\Presenters\UserPresenter;
use Robbo\Presenter\PresentableInterface;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\User
 */
class UserTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testIsPresentable()
    {
        $user = new User();

        $this->assertInstanceOf(PresentableInterface::class, $user);
    }

    /**
     * @covers ::__construct
     */
    public function testIsAuthenticable()
    {
        $user = new User();

        $this->assertInstanceOf(Authenticatable::class, $user);
    }

    /**
     * @covers ::requestEmailToken
     */
    public function testRequestEmailToken()
    {
        $this->markTestIncomplete('not yet implemented');
    }

    /**
     * @covers ::getPresenter
     */
    public function testGetPresenter()
    {
        $user      = new User();
        $presenter = $user->getPresenter();

        $this->assertInstanceOf(UserPresenter::class, $presenter);
        $this->assertSame($user, $presenter->getObject());
    }

    /**
     * @covers ::__get
     */
    public function testGetAvatarUrl()
    {
        $this->markTestIncomplete('not yet implemented');
    }

    /**
     * @covers ::getHasTwoFactorAuthenticationAttribute
     */
    public function testGetHasTwoFactorAuthenticationAttributeReturnsFalseWhenNoneSet()
    {
        $user = new User();

        $this->assertFalse($user->has_two_factor_authentication);
    }

    /**
     * @covers ::getHasTwoFactorAuthenticationAttribute
     */
    public function testGetHasTwoFactorAuthenticationAttributeReturnsTrueWhenSet()
    {
        $user                   = new User();
        $user->google2fa_secret = 'a-2fa-secret';

        $this->assertTrue($user->has_two_factor_authentication);
    }

    /**
     * @covers ::sendPasswordResetNotification
     */
    public function testSendPasswordResetNotification()
    {
        $expectedToken = 'an-email-token';

        Notification::fake();

        $user = new User();
        $user->sendPasswordResetNotification($expectedToken);

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
