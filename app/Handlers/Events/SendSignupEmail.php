<?php namespace App\Handlers\Events;

use Lang;
use Mail;
use App\Events\UserWasCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;

/**
 * Sends an email when the user has been created
 */
class SendSignupEmail implements ShouldBeQueued
{
    use InteractsWithQueue;

    /**
     * Create the event handler.
     *
     * @return SendSignupEmail
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param UserWasCreated $event
     * @return void
     */
    public function handle(UserWasCreated $event)
    {
        $user = $event->user;

        $data = [
            'password' => $event->password,
            'email'    => $user->email
        ];

        Mail::send('emails.account', $data, function ($message) use ($user) {
            $message->to($user->email, $user->name)
                    ->subject(Lang::get('emails.creation_subject'));
        });
    }
}
