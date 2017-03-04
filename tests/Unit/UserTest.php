<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Notification;
use McCool\LaravelAutoPresenter\HasPresenter;
use REBELinBLUE\Deployer\Notifications\System\ResetPassword;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;
use REBELinBLUE\Deployer\View\Presenters\UserPresenter;

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

        $this->assertInstanceOf(HasPresenter::class, $user);
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
     * @covers ::getPresenterClass
     */
    public function testGetPresenterClass()
    {
        $user      = new User();
        $presenter = $user->getPresenterClass();

        $this->assertSame(UserPresenter::class, $presenter);
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
