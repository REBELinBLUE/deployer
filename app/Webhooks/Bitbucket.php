<?php

namespace REBELinBLUE\Deployer\Webhooks;

/**
 * Class to handle integration with Bitbucket webhooks.
 */
class Bitbucket extends Webhook
{
    /**
     * Determines whether the request was from Bitbucket.
     *
     * @return boolean
     */
    public function isRequestOrigin()
    {
        return ($this->request->headers->has('X-Event-Key'));
    }

    /**
     * Parses the request for a push webhook body.
     *
     * @return mixed Either an array of parameters for the deployment config, or false if it is invalid.
     */
    public function handlePush()
    {
        return false;
    }
}
