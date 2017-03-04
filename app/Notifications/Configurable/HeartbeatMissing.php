<?php

namespace REBELinBLUE\Deployer\Notifications\Configurable;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use NotificationChannels\HipChat\HipChatMessage;
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
    public function toMail(Channel $notification)
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
    public function toSlack(Channel $notification)
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
    public function toWebhook(Channel $notification)
    {
        return $this->buildWebhookMessage('heartbeat_missing', $notification);
    }

    /**
     * Get the Twilio version of the notification.
     *
     * @return TwilioMessage
     */
    public function toTwilio()
    {
        $translation = 'heartbeats.never_sms_message';
        if (!is_null($this->heartbeat->last_activity)) {
            $translation = 'heartbeats.missing_sms_message';
        }

        return $this->buildTwilioMessage($translation);
    }

    /**
     * Get the Hipchat version of the message.
     *
     * @param Channel $notification
     *
     * @return HipChatMessage
     */
    public function toHipchat(Channel $notification)
    {
        return $this->buildHipchatMessage(
            'heartbeats.missing_message',
            $notification
        )->error();
    }
}
