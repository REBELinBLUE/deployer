<?php

namespace REBELinBLUE\Deployer\Webhooks;

/**
 * Class to handle integration with Beanstalkapp webhooks.
 */
class Beanstalkapp extends Webhook
{
    /**
     * Determines whether the request was from Beanstalkapp.
     *
     * @return bool
     */
    public function isRequestOrigin()
    {
        return ($this->request->headers->get('User-Agent') === 'beanstalkapp.com');
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
