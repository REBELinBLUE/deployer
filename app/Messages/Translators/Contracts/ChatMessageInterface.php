<?php

namespace REBELinBLUE\Deployer\Messages\Translators\Contracts;

use REBELinBLUE\Deployer\Messages\Contracts\MessageInterface;
use REBELinBLUE\Deployer\Notification;

interface ChatMessageInterface
{
    public function __construct(Notification $notification, MessageInterface $message);
    public function getPayload();
}
