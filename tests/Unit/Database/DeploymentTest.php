<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Database;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\DeployStep;
use REBELinBLUE\Deployer\Events\ModelChanged;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Deployment
 * @group slow
 */
class DeploymentTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @covers ::user
     */
    public function testUser()
    {
        /** @var Deployment $deployment */
        $deployment = factory(Deployment::class)->create();
        $actual     = $deployment->user();

        $this->assertInstanceOf(BelongsTo::class, $actual);
        $this->assertSame('user', $actual->getRelation());
        $this->assertInstanceOf(User::class, $deployment->user);
    }

    /**
     * @covers ::user
     */
    public function testUserIncludesTrashedUser()
    {
        /** @var Deployment $deployment */
        $deployment = factory(Deployment::class)->create();

        Carbon::setTestNow(Carbon::create(2017, 2, 1, 15, 45, 00, 'UTC'));

        $deployment->user->delete();

        $this->assertInstanceOf(User::class, $deployment->user);
        $this->assertSame($deployment->user->id, $deployment->user_id);
        $this->assertSameTimestamp('2017-02-01 15:45:00', $deployment->user->deleted_at);
    }

    /**
     * @covers ::boot
     */
    public function testBoot()
    {
        $this->expectsEvents(ModelChanged::class);

        factory(Deployment::class)->create();
    }

    /**
     * @covers ::isCurrent
     */
    public function testIsCurrentReturnsTrueWhenIsLatestDeployment()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        $this->generateAdditionalDeployments($project);

        /** @var Deployment $deployment */
        $deployment = factory(Deployment::class)->create([
            'status'     => Deployment::COMPLETED,
            'project_id' => $project->id,
        ]);

        $this->assertTrue($deployment->isCurrent());
    }

    /**
     * @covers ::isCurrent
     */
    public function testIsCurrentReturnsFalseWhenNotLatestDeployment()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        /** @var Deployment $deployment */
        $deployment = factory(Deployment::class)->create([
            'status'     => Deployment::COMPLETED,
            'project_id' => $project->id,
        ]);

        $this->generateAdditionalDeployments($project);

        $this->assertFalse($deployment->isCurrent());
    }

    /**
     * @covers ::getProjectNameAttribute
     */
    public function testGetProjectNameAttribute()
    {
        $expected = 'a-test-project';

        /** @var Project $project */
        $project = factory(Project::class)->create([
           'name' => $expected,
        ]);

        /** @var Deployment $deployment */
        $deployment = factory(Deployment::class)->create([
            'project_id' => $project->id,
        ]);

        $this->assertSame($expected, $deployment->getProjectNameAttribute());
        $this->assertSame($expected, $deployment->project_name);
    }

    /**
     * @covers ::getReleaseIdAttribute
     */
    public function testGetReleaseIdAttribute()
    {
        /** @var Deployment $deployment */
        $deployment = factory(Deployment::class)->create([
            'started_at' => Carbon::create(2017, 10, 1, 12, 45, 15, 'UTC'),
        ]);

        $expected = '20171001124515';

        $this->assertSame($expected, $deployment->getReleaseIdAttribute());
        $this->assertSame($expected, $deployment->release_id);
    }

    /**
     * @covers ::getDeployerNameAttribute
     */
    public function testGetDeployerNameAttributeWithUser()
    {
        $expected = 'John';

        /** @var Deployment $deployment */
        $deployment = factory(Deployment::class)->make([
            'user_id' => function () use ($expected) {
                return factory(User::class)->create(['name' => $expected])->id;
            },
        ]);

        $this->assertSame($expected, $deployment->getDeployerNameAttribute());
        $this->assertSame($expected, $deployment->deployer_name);
    }

    /**
     * @covers ::getDeployerNameAttribute
     */
    public function testGetDeployerNameAttributeWithSource()
    {
        $expected = 'Github';

        /** @var Deployment $deployment */
        $deployment = factory(Deployment::class)->make([
            'user_id' => null,
            'source'  => $expected,
        ]);

        $this->assertSame($expected, $deployment->getDeployerNameAttribute());
        $this->assertSame($expected, $deployment->deployer_name);
    }

    /**
     * @covers ::getDeployerNameAttribute
     */
    public function testGetDeployerNameAttributeWithoutUserOrSource()
    {
        $expected = 'Bob Smith';

        /** @var Deployment $deployment */
        $deployment = factory(Deployment::class)->make([
            'user_id'   => null,
            'source'    => null,
            'committer' => $expected,
        ]);

        $this->assertSame($expected, $deployment->getDeployerNameAttribute());
        $this->assertSame($expected, $deployment->deployer_name);
    }

    /**
     * @covers ::steps
     */
    public function testSteps()
    {
        $expected = 4;

        /** @var Deployment $deployment */
        $deployment = factory(Deployment::class)->create();

        $actual = $deployment->steps();

        factory(DeployStep::class, $expected)->make()->each(function ($step) use ($deployment) {
            $deployment->steps()->save($step);
        });

        $this->assertInstanceOf(HasMany::class, $actual);
        $this->assertSame('deployment_id', $actual->getForeignKeyName());
        $this->assertCount($expected, $deployment->steps);
    }

    /**
     * @covers ::getCommandsAttribute
     * @covers ::loadCommands
     */
    public function testCommandAttributeWhenEmpty()
    {
        /** @var Deployment $deployment */
        $deployment = factory(Deployment::class)->create();
        $actual     = $deployment->getCommandsAttribute();

        $this->assertSame(0, $actual->count());
    }

    /**
     * @covers ::getCommandsAttribute
     * @covers ::loadCommands
     */
    public function testCommandAttributeWhenNotEmpty()
    {
        /** @var Deployment $deployment */
        $deployment = factory(Deployment::class)->create();

        /** @var DeployStep $step */
        $step = factory(DeployStep::class)->states('custom')->create([
            'deployment_id' => $deployment->id,
        ]);

        $original = $deployment->getCommandsAttribute();

        /** @var Command $command */
        $command = $original->get(0);

        // Add another step to ensure the value is cached
        factory(DeployStep::class)->states('custom')->create([
            'deployment_id' => $deployment->id,
        ]);

        $cached = $deployment->getCommandsAttribute();

        $this->assertSame(1, $original->count());
        $this->assertSame(1, $cached->count());
        $this->assertSame($step->command->id, $command->id);
        $this->assertSame($step->command->name, $command->name);
        $this->assertSame($step->command->script, $command->script);
        $this->assertSame($step->command->user, $command->user);
        $this->assertSame($step->command->step, $command->step);
    }

    private function generateAdditionalDeployments(Project $project, $total = 5)
    {
        factory(Deployment::class, $total)->create([
            'status'     => Deployment::COMPLETED,
            'project_id' => $project->id,
        ]);
    }
}
