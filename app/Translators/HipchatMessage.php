<?php

namespace REBELinBLUE\Deployer\Translators;

use REBELinBLUE\Deployer\Translators\Contracts\ChatMessageInterface;
use REBELinBLUE\Deployer\Message;
use REBELinBLUE\Deployer\Notification;

class HipchatMessage implements ChatMessageInterface
{
    private $message;
    private $notification;

    public function __construct(Notification $notification, Message $message)
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
