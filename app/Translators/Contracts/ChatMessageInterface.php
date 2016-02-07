<?php

namespace REBELinBLUE\Deployer\Translators\Contracts;

use REBELinBLUE\Deployer\Message;
use REBELinBLUE\Deployer\Notification;

interface ChatMessageInterface
{
    public function __construct(Notification $notification, Message $message);
    public function getPayload();
}
