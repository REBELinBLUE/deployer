<?php

namespace REBELinBLUE\Deployer\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification as BaseNotification;
use NotificationChannels\HipChat\HipChatChannel;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Webhook\WebhookChannel;
use REBELinBLUE\Deployer\Channel;

/**
 * Notification class.
 */
abstract class Notification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  Channel $notification
     * @return array
     */
    public function via(Channel $notification)
    {
        if ($notification->type === Channel::WEBHOOK) {
            return [WebhookChannel::class];
        }

        if ($notification->type === Channel::TWILIO) {
            return [TwilioChannel::class];
        }

        if ($notification->type === Channel::HIPCHAT) {
            return [HipChatChannel::class];
        }

        return [$notification->type];
    }
}
