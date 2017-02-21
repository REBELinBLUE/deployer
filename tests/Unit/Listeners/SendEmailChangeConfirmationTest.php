<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Listeners;

use Illuminate\Support\Facades\Notification;
use Mockery as m;
use REBELinBLUE\Deployer\Events\EmailChangeRequested;
use REBELinBLUE\Deployer\Listeners\SendEmailChangeConfirmation;
use REBELinBLUE\Deployer\Notifications\System\ChangeEmail;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Listeners\SendEmailChangeConfirmation
 */
class SendEmailChangeConfirmationTest extends TestCase
{
    /**
     * @covers ::handle
     */
    public function testHandleDispatchesNotification()
    {
        Notification::fake();

        // Prevent a query from running on the DB
        $user = m::mock(User::class);
        $user->shouldReceive('requestEmailToken')->once()->andReturn('an-expected-token');
        $user->shouldDeferMissing();

        $event = new EmailChangeRequested($user);

        $listener = new SendEmailChangeConfirmation();
        $listener->handle($event);

        Notification::assertSentTo($user, ChangeEmail::class);
    }
}
