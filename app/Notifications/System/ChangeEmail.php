<?php

namespace REBELinBLUE\Deployer\Notifications\System;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use REBELinBLUE\Deployer\User;

/**
 * Notification sent when changing email address.
 */
class ChangeEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var string
     */
    private $token;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * Create a new notification instance.
     *
     * @param string     $token
     * @param Translator $translator
     */
    public function __construct($token, Translator $translator)
    {
        $this->token      = $token;
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
        $action = route('profile.confirm-change-email', ['token' => $this->token]);

        return (new MailMessage())
            ->view(['notifications.email', 'notifications.email-plain'], [
                'name' => $user->name,
            ])
            ->subject($this->translator->get('emails.confirm_email'))
            ->line($this->translator->get('emails.change_header'))
            ->line($this->translator->get('emails.change_below'))
            ->action($this->translator->get('emails.login_change'), $action)
            ->line($this->translator->get('emails.change_footer'));
    }
}
