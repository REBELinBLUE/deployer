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
class DeploymentSucceeded extends DeploymentFinished
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
            'deployments.success_email_subject',
            'deployments.success_email_message',
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
            'deployments.success_slack_message',
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
        return $this->buildWebhookMessage('deployment_succeeded', $notification);
    }

    /**
     * Gets the Twilio version of the notification.
     *
     * @return TwilioMessage
     */
    public function toTwilio(): TwilioMessage
    {
        return $this->buildTwilioMessage('deployments.success_sms_message');
    }

    /**
     * Gets the Hipchat version of the message.
     *
     * @param Channel $notification
     *
     * @return HipChatMessage
     */
    public function toHipchat(Channel $notification): HipChatMessage
    {
        return $this->buildHipchatMessage(
            'deployments.success_hipchat_message',
            $notification
        )->success();
    }
}
