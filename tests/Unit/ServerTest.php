<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Traits\TestsModel;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Server
 */
class ServerTest extends TestCase
{
    use TestsModel;

    /**
     * @covers ::project
     */
    public function testProject()
    {
        $server = new Server();
        $actual = $server->project();

        $this->assertInstanceOf(BelongsTo::class, $actual);
        $this->assertBelongsTo('project', Server::class);
    }

    /**
     * @dataProvider provideStatuses
     * @covers ::isTesting
     *
     * @param int  $status
     * @param bool $expected
     */
    public function testIsTesting(int $status, bool $expected)
    {
        $server = new Server();

        $this->assertFalse($server->isTesting());

        $server->status = $status;

        $this->assertSame($expected, $server->isTesting());
    }

    public function provideStatuses(): array
    {
        return $this->fixture('Server')['statuses'];
    }

    /**
     * @dataProvider providePaths
     * @covers ::getCleanPathAttribute
     *
     * @param string $path
     * @param string $expected
     */
    public function testGetCleanPathAttribute(string $path, string $expected)
    {
        $server       = new Server();
        $server->path = $path;

        $this->assertSame($expected, $server->clean_path);
        $this->assertSame($expected, $server->getCleanPathAttribute());
    }

    public function providePaths(): array
    {
        return $this->fixture('Server')['paths'];
    }
}
