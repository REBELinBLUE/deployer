<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events;

use Mockery as m;
use REBELinBLUE\Deployer\Events\UserWasCreated;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\UserWasCreated
 */
class UserWasCreatedTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testUserAndPasswordProperty()
    {
        $expected = 'a-plaintext-password';
        $user     = m::mock(User::class);

        $event = new UserWasCreated($user, $expected);

        $this->assertSame($user, $event->user);
        $this->assertSame($expected, $event->password);
    }
}
