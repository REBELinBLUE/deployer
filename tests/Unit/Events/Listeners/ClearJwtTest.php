<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events\Listeners;

use Illuminate\Session\Store;
use Mockery as m;
use REBELinBLUE\Deployer\Events\Listeners\ClearJwt;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\Listeners\ClearJwt
 */
class ClearJwtTest extends TestCase
{
    /**
     * @covers ::handle
     * @covers ::__construct
     */
    public function testHandleClearsSession()
    {
        $session = m::mock(Store::class);
        $session->shouldReceive('forget')->once()->with('jwt');

        $event = new ClearJwt($session);
        $event->handle();
    }
}
