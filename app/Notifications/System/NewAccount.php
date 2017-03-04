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
    public function via()
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
    public function toMail(User $user)
    {
        return (new MailMessage())
            ->view(['notifications.email', 'notifications.email-plain'], [
                'name' => $user->name,
            ])
            ->subject($this->translator->trans('emails.creation_subject'))
            ->line($this->translator->trans('emails.created'))
            ->line($this->translator->trans('emails.username', ['username' => $user->email]))
            ->line($this->translator->trans('emails.password', ['password' => $this->password]))
            ->action($this->translator->trans('emails.login_now'), route('dashboard'));
    }
}
