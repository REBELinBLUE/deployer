<?php

namespace REBELinBLUE\Deployer\Notifications\Configurable;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use NotificationChannels\Twilio\TwilioSmsMessage as TwilioMessage;
use NotificationChannels\Webhook\WebhookMessage;
use REBELinBLUE\Deployer\Channel;

/**
 * Notification sent when a heartbeat is missed.
 */
class HeartbeatMissing extends HeartbeatChanged
{
    /**
     * Get the mail representation of the notification.
     *
     * @param Channel $notification
     *
     * @return MailMessage
     */
    public function toMail(Channel $notification): MailMessage
    {
        return $this->buildMailMessage(
            'heartbeats.missing_subject',
            'heartbeats.missing_message',
            $notification
        )->error();
    }

    /**
     * Get the slack version of the notification.
     *
     * @param Channel $notification
     *
     * @return SlackMessage
     */
    public function toSlack(Channel $notification): SlackMessage
    {
        return $this->buildSlackMessage(
            'heartbeats.missing_message',
            $notification
        )->error();
    }

    /**
     * Get the webhook version of the notification.
     *
     * @param Channel $notification
     *
     * @return WebhookMessage
     */
    public function toWebhook(Channel $notification): WebhookMessage
    {
        return $this->buildWebhookMessage('heartbeat_missing', $notification);
    }

    /**
     * Get the Twilio version of the notification.
     *
     * @return TwilioMessage
     */
    public function toTwilio(): TwilioMessage
    {
        $translation = 'heartbeats.never_sms_message';
        if (!is_null($this->heartbeat->last_activity)) {
            $translation = 'heartbeats.missing_sms_message';
        }

        return $this->buildTwilioMessage($translation);
    }
}
