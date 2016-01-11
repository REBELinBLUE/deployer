<?php

namespace REBELinBLUE\Deployer\Listeners\Events;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Session;

/**
 * Event listener class to remove the JWT on logout.
 */
class ClearJwt extends Event
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Logout $event
     * @return void
     */
    public function handle(Logout $event)
    {
        Session::forget('jwt');
    }
}
