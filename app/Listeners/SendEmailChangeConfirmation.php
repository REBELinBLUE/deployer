<?php

namespace REBELinBLUE\Deployer\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use REBELinBLUE\Deployer\Events\EmailChangeRequested;
use REBELinBLUE\Deployer\Notifications\System\ChangeEmail;

/**
 * Request email change handler.
 */
class SendEmailChangeConfirmation implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param EmailChangeRequested $event
     */
    public function handle(EmailChangeRequested $event)
    {
        $token = $event->user->requestEmailToken();

        $event->user->notify(new ChangeEmail($token));
    }
}
