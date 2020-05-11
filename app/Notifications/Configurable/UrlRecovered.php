<?php

namespace REBELinBLUE\Deployer\Notifications\Configurable;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use NotificationChannels\Twilio\TwilioSmsMessage as TwilioMessage;
use NotificationChannels\Webhook\WebhookMessage;
use REBELinBLUE\Deployer\Channel;

/**
 * Notification sent when URL recovers.
 */
class UrlRecovered extends UrlChanged
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
            'checkUrls.recovered_subject',
            'checkUrls.recovered_message',
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
    public function toSlack(Channel $notification): SlackMessage
    {
        return $this->buildSlackMessage(
            'checkUrls.recovered_message',
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
    public function toWebhook(Channel $notification): WebhookMessage
    {
        return $this->buildWebhookMessage('link_recovered', $notification);
    }

    /**
     * Gets the Twilio version of the notification.
     *
     * @return TwilioMessage
     */
    public function toTwilio(): TwilioMessage
    {
        return $this->buildTwilioMessage('checkUrls.recovered_sms_message');
    }
}
