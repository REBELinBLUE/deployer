<?php

namespace REBELinBLUE\Deployer\Services\Webhooks;

use Illuminate\Http\Request;

/**
 * Generic Webhook class.
 */
abstract class Webhook
{
    /**
     * The HTTP request object.
     *
     * @var Request
     */
    protected $request;

    /**
     * Webhook constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Determines whether the request is from a particular service.
     *
     * @return bool
     */
    abstract public function isRequestOrigin(): bool;

    /**
     * Parses the request for a push webhook body.
     *
     * @return mixed Either an array of parameters for the deployment config, or false if it is invalid.
     */
    abstract public function handlePush();
}
