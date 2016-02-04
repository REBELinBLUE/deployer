<?php

namespace REBELinBLUE\Deployer\Decorators

use REBELinBLUE\Deployer\Message;
use REBELinBLUE\Deployer\Decorators\Contracts\ChatMessageInterface;

class SlackMessage implements ChatMessageInterface
{
    private $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function getPayload()
    {
        return [];
    }
}
