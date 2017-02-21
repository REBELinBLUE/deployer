<?php

namespace REBELinBLUE\Deployer\Notifications\Configurable;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use NotificationChannels\HipChat\HipChatMessage;
use NotificationChannels\Twilio\TwilioSmsMessage as TwilioMessage;
use NotificationChannels\Webhook\WebhookMessage;
use REBELinBLUE\Deployer\Channel;

/**
 * Notification which is sent when the heartbeat recovers.
 */
class HeartbeatRecovered extends HeartbeatChanged
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
            'heartbeats.recovered_subject',
            'heartbeats.recovered_message',
            $notification
        )->success();
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
            'heartbeats.recovered_message',
            $notification
        )->success();
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
        return $this->buildWebhookMessage('heartbeat_recovered', $notification);
    }

    /**
     * Gets the Twilio version of the notification.
     *
     * @return TwilioMessage
     */
    public function toTwilio()
    {
        return $this->buildTwilioMessage('heartbeats.recovered_sms_message');
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
        return $this->buildHipchatMessage(
            'heartbeats.recovered_message',
            $notification
        )->success();
    }
}
