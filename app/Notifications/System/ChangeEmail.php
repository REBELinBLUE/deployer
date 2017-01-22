<?php

namespace REBELinBLUE\Deployer\Notifications\System;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\User;

/**
 * Notification sent when changing email address.
 */
class ChangeEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var
     */
    private $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  User        $user
     * @return MailMessage
     */
    public function toMail(User $user)
    {
        $action = route('profile.confirm-change-email', ['token' => $this->token]);

        return (new MailMessage)
            ->view(['notifications.email', 'notifications.email-plain'], [
                'name' => $user->name,
            ])
            ->subject(Lang::get('emails.confirm_email'))
            ->line(Lang::get('emails.change_header'))
            ->line(Lang::get('emails.change_below'))
            ->action(Lang::get('emails.login_change'), $action)
            ->line(Lang::get('emails.change_footer'));
    }
}
