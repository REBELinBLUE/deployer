<?php

namespace REBELinBLUE\Deployer\Listeners\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use REBELinBLUE\Deployer\Events\EmailChangeRequested;
use REBELinBLUE\Deployer\Notifications\ChangeEmail;

/**
 * Request email change handler.
 */
class EmailChangeConfirmation extends Event implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * EmailChangeConfirmation constructor.
     */
    public function __construct()
    {
        //
    }

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
