<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events;

use Mockery as m;
use REBELinBLUE\Deployer\Events\EmailChangeRequested;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\EmailChangeRequested
 */
class EmailChangeRequestedTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testUserProperty()
    {
        $user = m::mock(User::class);

        $event = new EmailChangeRequested($user);

        $this->assertSame($user, $event->user);
    }
}
