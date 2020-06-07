<?php

namespace REBELinBLUE\Deployer\Notifications\System;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
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
     * @var Translator
     */
    private $translator;

    /**
     * Create a new notification instance.
     *
     * @param string     $password
     * @param Translator $translator
     */
    public function __construct($password, Translator $translator)
    {
        $this->password   = $password;
        $this->translator = $translator;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via(): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param User $user
     *
     * @return MailMessage
     */
    public function toMail(User $user): MailMessage
    {
        return (new MailMessage())
            ->view(['notifications.email', 'notifications.email-plain'], [
                'name' => $user->name,
            ])
            ->subject($this->translator->get('emails.creation_subject'))
            ->line($this->translator->get('emails.created'))
            ->line($this->translator->get('emails.username', ['username' => $user->email]))
            ->line($this->translator->get('emails.password', ['password' => $this->password]))
            ->action($this->translator->get('emails.login_now'), route('dashboard'));
    }
}
