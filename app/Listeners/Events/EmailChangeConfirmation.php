<?php

namespace REBELinBLUE\Deployer\Listeners\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Lang;
use Mail;
use REBELinBLUE\Deployer\Events\EmailChangeRequested;

/**
 * Request email change handler.
 */
class EmailChangeConfirmation implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  EmailChangeRequested $event
     * @return void
     */
    public function handle(EmailChangeRequested $event)
    {
        $user = $event->user;

        $data = [
            'email' => $user->email,
            'name'  => $user->name,
            'token' => $user->requestEmailToken(),
        ];

        Mail::queueOn(
            'low',
            'emails.change_email',
            $data,
            function (Message $message) use ($user) {
                $message->to($user->email, $user->name)
                ->subject(Lang::get('emails.confirm_email'));
            }
        );
    }
}
