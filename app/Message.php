<?php

namespace REBELinBLUE\Deployer;

/**
 * Generic class for holding messages.
 */
class Message
{
    private $message;

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function addField()
    {
    }
}
