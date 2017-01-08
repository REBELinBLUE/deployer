<?php

namespace REBELinBLUE\Deployer\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\User;

/**
 * Notification which is sent when passwords are reset.
 */
class ResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a notification instance.
     *
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @return array
     */
    public function via()
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  User                                           $user
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(User $user)
    {
        return (new MailMessage)
            ->view(['notifications.email', 'notifications.email-plain'], ['name' => $user->name])
            ->subject(Lang::get('emails.reset_subject'))
            ->line(Lang::get('emails.reset_header'))
            ->line(Lang::get('emails.reset_below'))
            ->action(Lang::get('emails.reset'), route('auth.reset-confirm', ['token' => $this->token]))
            ->line(Lang::get('emails.reset_footer'));
    }
}
