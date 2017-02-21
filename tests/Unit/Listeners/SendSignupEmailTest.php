<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Listeners;

use Illuminate\Support\Facades\Notification;
use REBELinBLUE\Deployer\Events\UserWasCreated;
use REBELinBLUE\Deployer\Listeners\SendSignupEmail;
use REBELinBLUE\Deployer\Notifications\System\NewAccount;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Listeners\SendSignupEmail
 */
class SendSignupEmailTest extends TestCase
{
    /**
     * @covers ::handle
     */
    public function testHandleDispatchesNotification()
    {
        Notification::fake();

        $user  = new User();
        $event = new UserWasCreated($user, 'a-new-password');

        $listener = new SendSignupEmail();
        $listener->handle($event);

        Notification::assertSentTo($user, NewAccount::class);
    }
}
