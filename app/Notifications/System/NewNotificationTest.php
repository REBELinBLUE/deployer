<?php

namespace REBELinBLUE\Deployer\Notifications\System;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Support\Facades\Lang;
use NotificationChannels\HipChat\HipChatMessage;
use NotificationChannels\Twilio\TwilioSmsMessage as TwilioMessage;
use NotificationChannels\Webhook\WebhookMessage;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Notifications\Notification;

/**
 * Notification sent to confirm a new channel is configured correctly.
 */
class NewNotificationTest extends Notification
{
    /**
     * Get the mail representation of the notification.
     *
     * @param  Channel     $notification
     * @return MailMessage
     */
    public function toMail(Channel $notification)
    {
        return (new MailMessage)
            ->view(['notifications.email', 'notifications.email-plain'], [
                'name' => $notification->name,
            ])
            ->to($notification->config->email)
            ->subject(Lang::get('notifications.test_subject'))
            ->line(Lang::get('notifications.test_message'));
    }

    /**
     * Get the slack version of the notification.
     *
     * @param  Channel      $notification
     * @return SlackMessage
     */
    public function toSlack(Channel $notification)
    {
        return (new SlackMessage)
            ->to($notification->config->channel)
            ->content(Lang::get('notifications.test_slack_message'));
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
        return (new WebhookMessage)
            ->data([
                'message' => Lang::get('notifications.test_message'),
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
        return (new TwilioMessage)
            ->content(Lang::get('notifications.test_message'));
    }

    /**
     * Gets the Hipchat version of the message.
     *
     * @param  Channel        $notification
     * @return HipChatMessage
     */
    public function toHipchat(Channel $notification)
    {
        return (new HipChatMessage)
            ->room($notification->config->room)
            ->text(Lang::get('notifications.test_hipchat_message'));
    }
}
