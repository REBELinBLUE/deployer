<?php

namespace REBELinBLUE\Deployer\Events\Listeners;

use Illuminate\Session\Store;

/**
 * Event listener class to remove the JWT on logout.
 */
class ClearJwt
{
    /**
     * @var Store
     */
    private $session;

    /**
     * ClearJwt constructor.
     *
     * @param Store $session
     */
    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    /**
     * Handle the event.
     */
    public function handle()
    {
        $this->session->forget('jwt');
    }
}
