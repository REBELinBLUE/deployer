<?php

namespace REBELinBLUE\Deployer\Webhooks;

/**
 * Class to handle integration with Gitlab webhooks.
 */
class Gitlab extends Webhook
{
    /**
     * Determines whether the request was from Gitlab.
     *
     * @return boolean
     */
    public function isRequestOrigin()
    {
        return ($this->request->headers->has('X-Gitlab-Event');
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
