<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events\Listeners;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Facades\Notification;
use Mockery as m;
use REBELinBLUE\Deployer\Events\EmailChangeRequested;
use REBELinBLUE\Deployer\Events\Listeners\SendEmailChangeConfirmation;
use REBELinBLUE\Deployer\Notifications\System\ChangeEmail;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\Listeners\SendEmailChangeConfirmation
 */
class SendEmailChangeConfirmationTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleDispatchesNotification()
    {
        Notification::fake();

        // Prevent a query from running on the DB
        $user = m::mock(User::class);
        $user->shouldReceive('requestEmailToken')->once()->andReturn('an-expected-token');
        $user->shouldDeferMissing();

        $translator = m::mock(Translator::class);

        $event = new EmailChangeRequested($user);

        $listener = new SendEmailChangeConfirmation($translator);
        $listener->handle($event);

        Notification::assertSentTo($user, ChangeEmail::class);
    }
}
