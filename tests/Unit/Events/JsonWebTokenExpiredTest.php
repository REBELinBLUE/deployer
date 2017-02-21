<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events;

use Mockery as m;
use REBELinBLUE\Deployer\Events\JsonWebTokenExpired;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\JsonWebTokenExpired
 */
class JsonWebTokenExpiredTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testUserProperty()
    {
        $user = m::mock(User::class);

        $event = new JsonWebTokenExpired($user);

        $this->assertSame($user, $event->user);
    }
}
