<?php

namespace REBELinBLUE\Deployer\Decorators;

use REBELinBLUE\Deployer\Message;
use REBELinBLUE\Deployer\Notification;
use REBELinBLUE\Deployer\Decorators\Contracts\ChatMessageInterface;

class SlackMessage implements ChatMessageInterface
{
    private $message;
    private $notification;

    public function __construct(Notification $notification, Message $message)
    {
        $this->message = $message;
        $this->notification = $notification;
    }

    public function getPayload()
    {
        $payload = [
            'channel' => $this->notification->channel,
            'text' => $this->message->getMessage(),
        ];

        if (!empty($this->notification->icon)) {
            $icon_field = 'icon_url';
            if (preg_match('/:(.*):/', $this->notification->icon)) {
                $icon_field = 'icon_emoji';
            }

            $payload[$icon_field] = $this->notification->icon;
        }

        return $payload;
    }
}
