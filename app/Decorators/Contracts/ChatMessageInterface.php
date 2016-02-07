<?php

namespace REBELinBLUE\Deployer\Decorators\Contracts;

use REBELinBLUE\Deployer\Message;
use REBELinBLUE\Deployer\Notification;

interface ChatMessageInterface
{
    public function __construct(Notification $notification, Message $message);
    public function getPayload();
}
