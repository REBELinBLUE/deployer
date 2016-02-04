<?php

namespace REBELinBLUE\Deployer\Decorators\Contracts;

use REBELinBLUE\Deployer\Message;

interface ChatMessageInterface
{
    public function __constract(Message $message);
    public function getPayload();
}
