<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs\QueueDeployment;

use Illuminate\Support\Collection;
use Mockery as m;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Jobs\QueueDeployment\GroupedCommandListTransformer;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\QueueDeployment\GroupedCommandListTransformer
 */
class GroupedCommandListTransformerTest extends TestCase
{
    /**
     * @covers ::groupCommandsByDeployStep
     * @covers ::emptyStep
     * @covers ::step
     * @covers ::when
     */
    public function testGroupCommandsReturnsExpectedCollectionWhenNoCommands()
    {
        $commands = new Collection([]);
        $expected = [
            Command::DO_CLONE    => ['before' => [], 'after' => []],
            Command::DO_INSTALL  => ['before' => [], 'after' => []],
            Command::DO_ACTIVATE => ['before' => [], 'after' => []],
            Command::DO_PURGE    => ['before' => [], 'after' => []],
        ];

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->with('commands')->andReturn($commands);

        $transformer = new GroupedCommandListTransformer();
        $actual      = $transformer->groupCommandsByDeployStep($project);

        $this->assertSame($expected, $actual->toArray());
    }
}
