<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Database;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Traits\BroadcastChanges;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Server
 * @group slow
 */
class ServerTest extends TestCase
{
    use DatabaseMigrations, BroadcastChanges;

    /**
     * @covers ::setPortAttribute
     * @covers ::setAttributeStatusUntested
     */
    public function testSetPortAttribute()
    {
        $server = $this->getSuccessfulServer();

        $server->port = 22;

        $this->assertIsUntested($server);
    }

    /**
     * @covers ::setPortAttribute
     * @covers ::setAttributeStatusUntested
     */
    public function testSetPortAttributeDoesNotChangeStatusWhenSame()
    {
        $server = $this->getSuccessfulServer();

        $server->port = 2222;

        $this->assertIsSuccessful($server);
    }

    /**
     * @covers ::setIpAddressAttribute
     * @covers ::setAttributeStatusUntested
     */
    public function testSetIpAddressAttribute()
    {
        $server = $this->getSuccessfulServer();

        $server->ip_address = '127.0.0.1';

        $this->assertIsUntested($server);
    }

    /**
     * @covers ::setIpAddressAttribute
     * @covers ::setAttributeStatusUntested
     */
    public function testSetIpAddressDoesNotChangeStatusWhenSame()
    {
        $server = $this->getSuccessfulServer();

        $server->ip_address = '0.0.0.0';

        $this->assertIsSuccessful($server);
    }

    /**
     * @covers ::setPathAttribute
     * @covers ::setAttributeStatusUntested
     */
    public function testSetPathAttribute()
    {
        $server = $this->getSuccessfulServer();

        $server->path = '/var/www/deployer';

        $this->assertIsUntested($server);
    }

    /**
     * @covers ::setPathAttribute
     * @covers ::setAttributeStatusUntested
     */
    public function testSetPathAttributeDoesNotChangeStatusWhenSame()
    {
        $server = $this->getSuccessfulServer();

        $server->path = '/var/www';

        $this->assertIsSuccessful($server);
    }

    /**
     * @covers ::setUserAttribute
     * @covers ::setAttributeStatusUntested
     */
    public function testSetUserAttribute()
    {
        $server = $this->getSuccessfulServer();

        $server->user = 'deploy';

        $this->assertIsUntested($server);
    }

    /**
     * @covers ::setUserAttribute
     * @covers ::setAttributeStatusUntested
     */
    public function testSetUserAttributeDoesNotChangeStatusWhenSame()
    {
        $server = $this->getSuccessfulServer();

        $server->user = 'root';

        $this->assertIsSuccessful($server);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastCreatedEvent()
    {
        $this->assertBroadcastCreatedEvent(Server::class);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastUpdatedEvent()
    {
        $this->assertBroadcastUpdatedEvent(Server::class, [
            'status' => Server::UNTESTED,
        ], [
            'status' => Server::SUCCESSFUL,
        ]);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastTrashedEvent()
    {
        $this->assertBroadcastTrashedEvent(Server::class);
    }

    private function assertIsUntested(Server $server)
    {
        $this->assertSame(Server::UNTESTED, $server->status);
    }

    private function assertIsSuccessful(Server $server)
    {
        $this->assertSame(Server::SUCCESSFUL, $server->status);
    }

    /**
     * @return Server
     */
    private function getSuccessfulServer()
    {
        return factory(Server::class)->make([
            'user'       => 'root',
            'ip_address' => '0.0.0.0',
            'port'       => 2222,
            'path'       => '/var/www',
            'status'     => Server::SUCCESSFUL,
        ]);
    }
}
