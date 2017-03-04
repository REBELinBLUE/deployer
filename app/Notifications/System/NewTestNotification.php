<?php

namespace REBELinBLUE\Deployer\Notifications\System;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use NotificationChannels\HipChat\HipChatMessage;
use NotificationChannels\Twilio\TwilioSmsMessage as TwilioMessage;
use NotificationChannels\Webhook\WebhookMessage;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Notifications\Notification;

/**
 * Notification sent to confirm a new channel is configured correctly.
 */
class NewTestNotification extends Notification
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param Channel $notification
     *
     * @return MailMessage
     */
    public function toMail(Channel $notification)
    {
        return (new MailMessage())
            ->view(['notifications.email', 'notifications.email-plain'], [
                'name' => $notification->name,
            ])
            ->subject($this->translator->trans('notifications.test_subject'))
            ->line($this->translator->trans('notifications.test_message'));
    }

    /**
     * Get the slack version of the notification.
     *
     * @param Channel $notification
     *
     * @return SlackMessage
     */
    public function toSlack(Channel $notification)
    {
        return (new SlackMessage())
            ->to($notification->config->channel)
            ->content($this->translator->trans('notifications.test_slack_message'));
    }

    /**
     * Get the webhook version of the notification.
     *
     * @param Channel $notification
     *
     * @return WebhookMessage
     */
    public function toWebhook(Channel $notification)
    {
        return (new WebhookMessage())
            ->data([
                'message' => $this->translator->trans('notifications.test_message'),
            ])
            ->header('X-Deployer-Project-Id', $notification->project_id)
            ->header('X-Deployer-Notification-Id', $notification->id)
            ->header('X-Deployer-Event', 'notification_test');
    }

    /**
     * Gets the Twilio version of the notification.
     *
     * @return TwilioMessage
     */
    public function toTwilio()
    {
        return (new TwilioMessage())
            ->content($this->translator->trans('notifications.test_message'));
    }

    /**
     * Gets the Hipchat version of the message.
     *
     * @param Channel $notification
     *
     * @return HipChatMessage
     */
    public function toHipchat(Channel $notification)
    {
        return (new HipChatMessage())
            ->room($notification->config->room)
            ->text($this->translator->trans('notifications.test_hipchat_message'));
    }
}
