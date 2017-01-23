<?php

namespace REBELinBLUE\Deployer\Notifications\Configurable;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use NotificationChannels\HipChat\HipChatMessage;
use NotificationChannels\Twilio\TwilioSmsMessage as TwilioMessage;
use NotificationChannels\Webhook\WebhookMessage;
use REBELinBLUE\Deployer\Channel;

/**
 * Notification sent when a deployment succeeds.
 */
class DeploymentFailed extends DeploymentFinished
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
            'deployments.failed_email_subject',
            'deployments.failed_email_message',
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
            'deployments.failed_slack_message',
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
        return $this->buildWebhookMessage('deployment_failed', $notification);
    }

    /**
     * Gets the Twilio version of the notification.
     *
     * @return TwilioMessage
     */
    public function toTwilio()
    {
        return $this->buildTwilioMessage('deployments.failed_sms_message');
    }

    /**
     * Gets the Hipchat version of the message.
     *
     * @param  Channel        $notification
     * @return HipChatMessage
     */
    public function toHipchat(Channel $notification)
    {
        return $this->buildHipchatMessage(
            'deployments.failed_hipchat_message',
            $notification
        )->error();
    }
}
