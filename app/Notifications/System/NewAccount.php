<?php

namespace REBELinBLUE\Deployer\Notifications\System;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\User;

/**
 * Notification sent when a new account is created.
 */
class NewAccount extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var string
     */
    private $password;

    /**
     * Create a new notification instance.
     *
     * @param string $password
     */
    public function __construct($password)
    {
        $this->password = $password;
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
        return (new MailMessage)
            ->view(['notifications.email', 'notifications.email-plain'], [
                'name' => $user->name,
            ])
            ->subject(Lang::get('emails.creation_subject'))
            ->line(Lang::get('emails.created'))
            ->line(Lang::get('emails.username', ['username' => $user->email]))
            ->line(Lang::get('emails.password', ['password' => $this->password]))
            ->action(Lang::get('emails.login_now'), route('dashboard'));
    }
}
