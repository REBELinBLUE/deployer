<?php

namespace REBELinBLUE\Deployer\Messages\Translators;

use REBELinBLUE\Deployer\Messages\Contracts\MessageInterface;
use REBELinBLUE\Deployer\Notification;
use REBELinBLUE\Deployer\Messages\Translators\Contracts\ChatMessageInterface;

class HipchatMessage implements ChatMessageInterface
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
            'message'        => $this->getMessage(),
            'message_format' => 'text',
        ];

        return $payload;
    }

    private function getMessage()
    {
        $message = $this->message->getMessage();

        $message = str_replace(':+1:', '(corpsethumb)', $message);

        return $message;
    }
}
