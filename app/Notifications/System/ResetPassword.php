<?php

namespace REBELinBLUE\Deployer\Notifications\System;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
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
    private $token;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * Create a notification instance.
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
     * @param User $user
     *
     * @return MailMessage
     */
    public function toMail(User $user)
    {
        $action = route('auth.reset-confirm', ['token' => $this->token]);

        return (new MailMessage())
            ->view(['notifications.email', 'notifications.email-plain'], [
                'name' => $user->name,
            ])
            ->subject($this->translator->trans('emails.reset_subject'))
            ->line($this->translator->trans('emails.reset_header'))
            ->line($this->translator->trans('emails.reset_below'))
            ->action($this->translator->trans('emails.reset'), $action)
            ->line($this->translator->trans('emails.reset_footer'));
    }
}
