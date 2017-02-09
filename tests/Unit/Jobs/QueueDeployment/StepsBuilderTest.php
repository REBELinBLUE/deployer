<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs\QueueDeployment;

use Illuminate\Support\Collection;
use Mockery as m;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Jobs\QueueDeployment\StepsBuilder;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\DeployStepRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ServerLogRepositoryInterface;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\QueueDeployment\StepsBuilder
 */
class StepsBuilderTest extends TestCase
{
    /**
     * @var DeployStepRepositoryInterface
     */
    private $repository;

    /**
     * @var Project
     */
    private $project;

    /**
     * @var ServerLogRepositoryInterface
     */
    private $log;

    /**
     * @var Collection
     */
    private $grouped;

    /**
     * @var int
     */
    private $deployment_id = 1213;

    /**
     * @var Collection
     */
    private $servers;

    public function setUp()
    {
        parent::setUp();

        $project_id = 10;

        $server1 = factory(Server::class)->make([
            'project_id'  => $project_id,
            'deploy_code' => true,
        ]);
        $server1->id = 10;

        $server2 = factory(Server::class)->make([
            'project_id'  => $project_id,
            'deploy_code' => false,
        ]);
        $server2->id = 12;

        $servers = new Collection([$server1, $server2]);

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->with('servers')->andReturn($servers);

        // FIXME: We don't need this any more, we can just have 1 collection and filter it
        $grouped = new Collection([
            Command::DO_CLONE    => null,
            Command::DO_INSTALL  => null,
            Command::DO_ACTIVATE => null,
            Command::DO_PURGE    => null,
        ]);

        $grouped->keys()->each(function ($key) use ($grouped) {
            $grouped->put($key, new Collection([
                'before' => new Collection(),
                'after'  => new Collection(),
            ]));
        });

        $repository = m::mock(DeployStepRepositoryInterface::class);
        $repository->shouldReceive('create')->once()->with([
            'stage'         => Command::DO_CLONE,
            'deployment_id' => $this->deployment_id,
        ])->andReturn((object) ['id' => 12]);

        $repository->shouldReceive('create')->once()->with([
            'stage'         => Command::DO_INSTALL,
            'deployment_id' => $this->deployment_id,
        ])->andReturn((object) ['id' => 15]);

        $repository->shouldReceive('create')->once()->with([
            'stage'         => Command::DO_ACTIVATE,
            'deployment_id' => $this->deployment_id,
        ])->andReturn((object) ['id' => 20]);

        $repository->shouldReceive('create')->once()->with([
            'stage'         => Command::DO_PURGE,
            'deployment_id' => $this->deployment_id,
        ])->andReturn((object) ['id' => 25]);

        $log = m::mock(ServerLogRepositoryInterface::class);
        $log->shouldReceive('create')->once()->with(['server_id' => 10, 'deploy_step_id' => 12]);
        $log->shouldReceive('create')->once()->with(['server_id' => 10, 'deploy_step_id' => 15]);
        $log->shouldReceive('create')->once()->with(['server_id' => 10, 'deploy_step_id' => 20]);
        $log->shouldReceive('create')->once()->with(['server_id' => 10, 'deploy_step_id' => 25]);

        $this->repository = $repository;
        $this->log        = $log;
        $this->project    = $project;
        $this->grouped    = $grouped;
        $this->servers    = $servers;
    }

    /**
     * @covers ::__construct
     * @covers ::build
     * @covers ::createDeployStep
     */
    public function testBuildWithNoAdditionalCommandSteps()
    {
        $builder = new StepsBuilder($this->repository, $this->log);
        $builder->build($this->grouped, $this->project, $this->deployment_id, []);
    }

    /**
     * @covers ::__construct
     * @covers ::build
     * @covers ::createCustomSteps
     * @covers ::createDeployStep
     * @covers ::createCustomSteps
     * @covers ::shouldIncludeCommand
     */
    public function testBuildWithAdditionalCommandSteps()
    {
        $command1 = factory(Command::class)->make([
            'stage'         => Command::BEFORE_CLONE,
            'optional'      => false,
            'deployment_id' => $this->deployment_id,
        ]);

        $command1->id      = 10;
        $command1->servers = new Collection();

        $command2 = factory(Command::class)->make([
            'stage'         => Command::AFTER_INSTALL,
            'optional'      => false,
            'deployment_id' => $this->deployment_id,
        ]);

        $command2->id      = 12;
        $command2->servers = new Collection();

        $this->grouped->get(Command::DO_CLONE)->get('before')->push($command1);
        $this->grouped->get(Command::DO_INSTALL)->get('after')->push($command2);

        $this->repository->shouldReceive('create')->once()->with([
            'stage'         => Command::BEFORE_CLONE,
            'deployment_id' => $this->deployment_id,
            'command_id'    => $command1->id,
        ])->andReturn((object) ['id' => 5]);

        $this->repository->shouldReceive('create')->once()->with([
            'stage'         => Command::AFTER_INSTALL,
            'deployment_id' => $this->deployment_id,
            'command_id'    => $command2->id,
        ])->andReturn((object) ['id' => 18]);

        $builder = new StepsBuilder($this->repository, $this->log);
        $builder->build($this->grouped, $this->project, $this->deployment_id, []);
    }

    /**
     * @covers ::__construct
     * @covers ::build
     * @covers ::createCustomSteps
     * @covers ::createDeployStep
     * @covers ::createCommandStep
     * @covers ::shouldIncludeCommand
     */
    public function testBuildWithAdditionalCommandStepsCreatesServerLogs()
    {
        $command = factory(Command::class)->make([
            'stage'         => Command::BEFORE_CLONE,
            'optional'      => false,
            'deployment_id' => $this->deployment_id,
        ]);

        $command->id      = 10;
        $command->servers = $this->servers;

        $this->grouped->get(Command::DO_CLONE)->get('before')->push($command);

        $this->repository->shouldReceive('create')->once()->with([
            'stage'         => Command::BEFORE_CLONE,
            'deployment_id' => $this->deployment_id,
            'command_id'    => $command->id,
        ])->andReturn((object) ['id' => 5]);

        $this->log->shouldReceive('create')->once()->with(['server_id' => 10, 'deploy_step_id' => 5]);
        $this->log->shouldReceive('create')->once()->with(['server_id' => 12, 'deploy_step_id' => 5]);

        $builder = new StepsBuilder($this->repository, $this->log);
        $builder->build($this->grouped, $this->project, $this->deployment_id, []);
    }

    /**
     * @covers ::__construct
     * @covers ::build
     * @covers ::createCustomSteps
     * @covers ::createDeployStep
     * @covers ::createCommandStep
     * @covers ::shouldIncludeCommand
     */
    public function testBuildSkipsOptionalCommandsIfNotSelected()
    {
        $command = factory(Command::class)->make([
            'stage'         => Command::BEFORE_CLONE,
            'optional'      => true,
            'deployment_id' => $this->deployment_id,
        ]);

        $command->id      = 10;
        $command->servers = $this->servers;

        $this->grouped->get(Command::DO_CLONE)->get('before')->push($command);

        $this->repository->shouldNotReceive('create')->with([
            'stage'         => Command::BEFORE_CLONE,
            'deployment_id' => $this->deployment_id,
            'command_id'    => $command->id,
        ])->andReturn((object) ['id' => 5]);

        $this->log->shouldNotReceive('create')->with(['server_id' => 10, 'deploy_step_id' => 5]);
        $this->log->shouldNotReceive('create')->with(['server_id' => 12, 'deploy_step_id' => 5]);

        $builder = new StepsBuilder($this->repository, $this->log);
        $builder->build($this->grouped, $this->project, $this->deployment_id, []);
    }

    /**
     * @covers ::__construct
     * @covers ::build
     * @covers ::createCustomSteps
     * @covers ::createDeployStep
     * @covers ::createCommandStep
     * @covers ::shouldIncludeCommand
     */
    public function testBuildIncludesOptionalCommandsIfSelected()
    {
        $command1 = factory(Command::class)->make([
            'stage'         => Command::BEFORE_CLONE,
            'optional'      => false,
            'deployment_id' => $this->deployment_id,
        ]);

        $command1->id      = 10;
        $command1->servers = $this->servers;

        $command2 = factory(Command::class)->make([
            'stage'         => Command::AFTER_INSTALL,
            'optional'      => true,
            'deployment_id' => $this->deployment_id,
        ]);

        $command2->id      = 11;
        $command2->servers = $this->servers;

        $this->grouped->get(Command::DO_CLONE)->get('before')->push($command1);
        $this->grouped->get(Command::DO_INSTALL)->get('after')->push($command2);

        $this->repository->shouldReceive('create')->once()->with([
            'stage'         => Command::BEFORE_CLONE,
            'deployment_id' => $this->deployment_id,
            'command_id'    => $command1->id,
        ])->andReturn((object) ['id' => 5]);

        $this->repository->shouldReceive('create')->once()->with([
            'stage'         => Command::AFTER_INSTALL,
            'deployment_id' => $this->deployment_id,
            'command_id'    => $command2->id,
        ])->andReturn((object) ['id' => 6]);

        $this->log->shouldReceive('create')->once()->with(['server_id' => 10, 'deploy_step_id' => 5]);
        $this->log->shouldReceive('create')->once()->with(['server_id' => 12, 'deploy_step_id' => 5]);
        $this->log->shouldReceive('create')->once()->with(['server_id' => 10, 'deploy_step_id' => 6]);
        $this->log->shouldReceive('create')->once()->with(['server_id' => 12, 'deploy_step_id' => 6]);

        $builder = new StepsBuilder($this->repository, $this->log);
        $builder->build($this->grouped, $this->project, $this->deployment_id, [11]);
    }
}
