<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Server
 */
class ServerTest extends TestCase
{
    /**
     * @covers ::project
     */
    public function testProjectRelation()
    {
        $server = new Server();
        $actual = $server->project();

        $this->assertInstanceOf(BelongsTo::class, $actual);
        $this->assertSame('project', $actual->getRelation());
    }

    /**
     * @dataProvider getStatuses
     * @covers ::isTesting
     */
    public function testIsTesting($status, $expected)
    {
        $server = new Server();

        $this->assertFalse($server->isTesting());

        $server->status = $status;

        $this->assertSame($expected, $server->isTesting());
    }

    public function getStatuses()
    {
        return [
            [Server::TESTING, true],
            [Server::UNTESTED, false],
            [Server::SUCCESSFUL, false],
            [Server::FAILED, false],
        ];
    }

    /**
     * @dataProvider getPaths
     * @covers ::getCleanPathAttribute
     */
    public function testGetCleanPathAttribute($path, $expected)
    {
        $server       = new Server();
        $server->path = $path;

        $this->assertSame($expected, $server->clean_path);
        $this->assertSame($expected, $server->getCleanPathAttribute());
    }

    public function getPaths()
    {
        return [
            ['/var/www/deployer', '/var/www/deployer'],
            ['/var/www/deployer/', '/var/www/deployer'],
            ['/', ''],
        ];
    }

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

    private function assertIsUntested(Server $server)
    {
        $this->assertSame(Server::UNTESTED, $server->status);
    }

    private function assertIsSuccessful(Server $server)
    {
        $this->assertSame(Server::SUCCESSFUL, $server->status);
    }

    private function getSuccessfulServer()
    {
        $server             = new Server();
        $server->user       = 'root';
        $server->ip_address = '0.0.0.0';
        $server->port       = 2222;
        $server->path       = '/var/www';
        $server->status     = Server::SUCCESSFUL;

        return $server;
    }
}
