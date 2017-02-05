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
    public function testProject()
    {
        $server = new Server();
        $actual = $server->project();

        $this->assertInstanceOf(BelongsTo::class, $actual);
        $this->assertSame('project', $actual->getRelation());
    }

    /**
     * @dataProvider provideStatuses
     * @covers ::isTesting
     */
    public function testIsTesting($status, $expected)
    {
        $server = new Server();

        $this->assertFalse($server->isTesting());

        $server->status = $status;

        $this->assertSame($expected, $server->isTesting());
    }

    public function provideStatuses()
    {
        return $this->fixture('Server')['statuses'];
    }

    /**
     * @dataProvider providePaths
     * @covers ::getCleanPathAttribute
     */
    public function testGetCleanPathAttribute($path, $expected)
    {
        $server       = new Server();
        $server->path = $path;

        $this->assertSame($expected, $server->clean_path);
        $this->assertSame($expected, $server->getCleanPathAttribute());
    }

    public function providePaths()
    {
        return $this->fixture('Server')['paths'];
    }
}
