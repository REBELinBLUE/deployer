<?php

namespace REBELinBLUE\Deployer\Listeners\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use REBELinBLUE\Deployer\Events\UserWasCreated;
use REBELinBLUE\Deployer\Notifications\NewAccount;

/**
 * Sends an email when the user has been created.
 */
class SendSignupEmail extends Event implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * SendSignupEmail constructor.
     */
    public function __construct()
    {
        //
    }

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
