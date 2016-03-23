<?php

namespace REBELinBLUE\Deployer\Messages\Services\Slack;

use REBELinBLUE\Deployer\Messages\Contracts\MessageInterface;
use Illuminate\Support\Facades\Lang;

class TestMessage implements MessageInterface
{
    public function getPayload()
    {
        return [
            'text' => Lang::get('notifications.test_message')
        ];
    }
}
