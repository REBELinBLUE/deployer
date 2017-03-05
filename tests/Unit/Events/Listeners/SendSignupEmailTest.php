<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events\Listeners;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Facades\Notification;
use Mockery as m;
use REBELinBLUE\Deployer\Events\Listeners\SendSignupEmail;
use REBELinBLUE\Deployer\Events\UserWasCreated;
use REBELinBLUE\Deployer\Notifications\System\NewAccount;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\Listeners\SendSignupEmail
 */
class SendSignupEmailTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleDispatchesNotification()
    {
        Notification::fake();

        $user  = new User();
        $event = new UserWasCreated($user, 'a-new-password');

        $translator = m::mock(Translator::class);

        $listener = new SendSignupEmail($translator);
        $listener->handle($event);

        Notification::assertSentTo($user, NewAccount::class);
    }
}
