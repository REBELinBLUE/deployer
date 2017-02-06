<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Traits\HasTarget;
use REBELinBLUE\Deployer\Tests\Unit\Traits\TestsModel;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Command
 */
class CommandTest extends TestCase
{
    use TestsModel, HasTarget;

    /**
     * @covers ::servers
     */
    public function testServers()
    {
        $command = new Command();
        $actual  = $command->servers();

        // TODO: Test for the order by?
        $this->assertInstanceOf(BelongsToMany::class, $actual);
        $this->assertBelongsToMany('servers', Command::class);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\HasTarget
     */
    public function testTarget()
    {
        $this->assertHasTarget(Command::class);
    }
}
