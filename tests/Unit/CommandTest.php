<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\TestsModel;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Command
 */
class CommandTest extends TestCase
{
    use TestsModel;

    /**
     * @covers ::servers
     */
    public function testServers()
    {
        $command = new Command();
        $actual  = $command->servers();

        // TODO: Test for the order by?
        $this->assertInstanceOf(BelongsToMany::class, $actual);
        $this->assertBelongsTo('servers', Command::class);
    }
}
