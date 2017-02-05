<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Database;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\DeployStep;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\DeployStep
 * @group slow
 */
class DeployStepTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @covers ::servers
     */
    public function testServers()
    {
        /** @var DeployStep $step */
        $step = factory(DeployStep::class)->create();

        $actual = $step->servers();

        $this->assertInstanceOf(HasMany::class, $actual);
        $this->assertSame('deploy_step_id', $actual->getForeignKeyName());
    }

    /**
     * @covers ::command
     */
    public function testCommand()
    {
        /** @var DeployStep $step */
        $step = factory(DeployStep::class)->states('custom')->create();

        $actual = $step->command();

        $this->assertInstanceOf(BelongsTo::class, $actual);
        $this->assertSame('command', $actual->getRelation());
        $this->assertInstanceOf(Command::class, $step->command);
    }

    /**
     * @covers ::command
     */
    public function testCommandIsNullForDefaultStages()
    {
        /** @var DeployStep $step */
        $step = factory(DeployStep::class)->create();

        $this->assertNull($step->command);
    }

    /**
     * @covers ::command
     */
    public function testCommandIncludesTrashedCommand()
    {
        /** @var DeployStep $step */
        $step = factory(DeployStep::class)->states('custom')->create();

        Carbon::setTestNow(Carbon::create(2017, 2, 1, 15, 45, 00, 'UTC'));

        $step->command->delete();

        $this->assertInstanceOf(Command::class, $step->command);
        $this->assertSame($step->command->id, $step->command_id);
        $this->assertSameTimestamp('2017-02-01 15:45:00', $step->command->deleted_at);
    }

    /**
     * @dataProvider provideSteps
     * @covers ::isCustom
     */
    public function testIsCustom($stage, $expected)
    {
        /** @var DeployStep $step */
        $step = factory(DeployStep::class)->make([
            'stage'      => $stage,
            'command_id' => function () {
                return factory(Command::class)->create()->id;
            },
        ]);

        $this->assertSame($expected, $step->isCustom());
    }

    public function provideSteps()
    {
        return $this->fixture('DeployStep')['custom_steps'];
    }
}
