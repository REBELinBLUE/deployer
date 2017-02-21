<?php

namespace REBELinBLUE\Deployer\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use REBELinBLUE\Deployer\Events\UserWasCreated;
use REBELinBLUE\Deployer\Notifications\System\NewAccount;

/**
 * Sends an email when the user has been created.
 */
class SendSignupEmail implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param UserWasCreated $event
     */
    public function handle(UserWasCreated $event)
    {
        $event->user->notify(new NewAccount($event->password));
    }
}
