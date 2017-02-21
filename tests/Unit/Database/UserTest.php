<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Database;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Traits\BroadcastChanges;
use REBELinBLUE\Deployer\User;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\User
 * @group slow
 */
class UserTest extends TestCase
{
    use DatabaseMigrations, BroadcastChanges;

    /**
     * @covers ::requestEmailToken
     */
    public function testRequestEmailToken()
    {
        $expected = 'an-email-token';

        $this->mockTokenGenerator($expected);

        $user   = factory(User::class)->create();
        $actual = $user->requestEmailToken();

        $this->assertDatabaseHas('users', [
            'email_token' => $expected,
            'id'          => $user->id,
        ]);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastCreatedEvent()
    {
        $this->assertBroadcastCreatedEvent(User::class);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastUpdatedEvent()
    {
        $this->assertBroadcastUpdatedEvent(User::class, [
            'language' => 'en_US',
        ], [
            'language' => 'en_GB',
        ]);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastTrashedEvent()
    {
        $this->assertBroadcastTrashedEvent(User::class);
    }
}
