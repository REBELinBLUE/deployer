<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Listeners;

use Illuminate\Session\Store;
use Mockery as m;
use REBELinBLUE\Deployer\Listeners\ClearJwt;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Listeners\ClearJwt
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
