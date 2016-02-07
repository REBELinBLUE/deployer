<?php

namespace REBELinBLUE\Deployer\Messages\Translators;

use REBELinBLUE\Deployer\Messages\Contracts\MessageInterface;
use REBELinBLUE\Deployer\Notification;
use REBELinBLUE\Deployer\Messages\Translators\Contracts\ChatMessageInterface;

class SlackMessage implements ChatMessageInterface
{
    private $message;
    private $notification;

    public function __construct(Notification $notification, MessageInterface $message)
    {
        $this->message      = $message;
        $this->notification = $notification;
    }

    public function getPayload()
    {
        $payload = [
            'channel' => $this->notification->channel,
        ];

        if (!empty($this->notification->icon)) {
            $icon_field = 'icon_url';
            if (preg_match('/:(.*):/', $this->notification->icon)) {
                $icon_field = 'icon_emoji';
            }

            $payload[$icon_field] = $this->notification->icon;
        }

        return array_merge($payload, $this->messaget->getPayload());
    }
}
