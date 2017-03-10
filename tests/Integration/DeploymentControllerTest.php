<?php

namespace REBELinBLUE\Deployer\Tests\Integration;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Collection;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\ConfigFile;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Jobs\AbortDeployment;
use REBELinBLUE\Deployer\Jobs\QueueUpdateGitMirror;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\ServerLog;
use REBELinBLUE\Deployer\SharedFile;
use REBELinBLUE\Deployer\Variable;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\DeploymentController
 */
class DeploymentControllerTest extends AuthenticatedTestCase
{
    use DatabaseMigrations;

    /**
     * @covers ::__construct
     * @covers ::refresh
     */
    public function testRefresh()
    {
        $this->withoutEvents();

        factory(Project::class)->create();

        $this->expectsJobs(QueueUpdateGitMirror::class);

        $response = $this->postJson('/projects/1/refresh');
        $response->assertStatus(Response::HTTP_OK)->assertExactJson(['success' => true]);
    }

    /**
     * @covers ::__construct
     * @covers ::refresh
     */
    public function testRefreshReturnsErrorWhenInvalid()
    {
        $this->withoutEvents();
        $this->doesntExpectJobs(QueueUpdateGitMirror::class);

        $response = $this->postJson('/projects/1024/refresh');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers ::__construct
     * @covers ::abort
     */
    public function testAbort()
    {
        $this->withoutEvents();

        factory(Deployment::class)->create();

        $this->expectsJobs(AbortDeployment::class);

        $response = $this->post('/deployment/1/abort');
        $response->assertStatus(Response::HTTP_FOUND)->assertRedirect('/deployment/1');
    }

    /**
     * @covers ::__construct
     * @covers ::abort
     */
    public function testAbortReturnsErrorWhenInvalid()
    {
        $this->withoutEvents();
        $this->doesntExpectJobs(AbortDeployment::class);

        $response = $this->post('/deployment/121/abort');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers ::__construct
     * @covers ::log
     */
    public function testLogWithoutRuntime()
    {
        $this->withoutEvents();

        /** @var ServerLog $log */
        $log = factory(ServerLog::class)->create();

        $response = $this->getJson('/log/1');
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson($log->toArray())
                 ->assertJson(['runtime' => null]);
    }

    /**
     * @covers ::__construct
     * @covers ::log
     */
    public function testLogWithRuntime()
    {
        /** @var ServerLog $log */
        $log = factory(ServerLog::class)->create([
            'started_at'  => Carbon::create(2017, 1, 1, 12, 00, 00),
            'finished_at' => Carbon::create(2017, 1, 1, 12, 15, 00),
        ]);

        $response = $this->getJson('/log/1');
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson($log->toArray())
                 ->assertJson(['runtime' => '15 minutes']);
    }

    /**
     * @covers ::__construct
     * @covers ::log
     */
    public function testLogReturnsErrorWhenInvalid()
    {
        $response = $this->getJson('/log/9999');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers ::__construct
     * @covers ::project
     */
    public function testProject()
    {
        $this->withoutEvents();
        $this->withoutJobs();
        $this->withoutNotifications();

        $expectedOptional = 2;
        $expectedAlways   = 5;

        /** @var Project $project */
        $project = factory(Project::class)->create();

        factory(Command::class, $expectedAlways)->create([
            'target_type' => 'project',
            'target_id'   => $project->id,
            'optional'    => false,
        ]);

        /** @var Collection $optional */
        $optional = factory(Command::class, $expectedOptional)->create([
            'target_type' => 'project',
            'target_id'   => $project->id,
            'optional'    => true,
        ]);

        $config = [
            'project_id' => $project->id,
        ];

        $target = [
            'target_id'   => $project->id,
            'target_type' => 'project',
        ];

        factory(Server::class)->create($config);
        factory(Channel::class)->create($config);
        factory(Heartbeat::class)->create($config);
        factory(CheckUrl::class)->create($config);
        factory(SharedFile::class)->create($target);
        factory(ConfigFile::class)->create($target);
        factory(Variable::class)->create($target);

        $project = $this->app->make(ProjectRepositoryInterface::class)->getById($project->id);

        $response = $this->get('/projects/1');

        $response->assertStatus(Response::HTTP_OK)->assertViewHas([
            'target_type', 'target_id', 'optional', 'tags', 'branches', 'servers', 'channels', 'heartbeats',
            'sharedFiles', 'configFiles', 'checkUrls', 'variables', 'deployments', 'route',
        ]);

        /** @var \McCool\LaravelAutoPresenter\BasePresenter $view */
        $view = $response->getOriginalContent();

        $this->assertSame('project', $view->target_type);
        $this->assertSame($project->id, $view->target_id);
        $this->assertSame($project->id, $view->project->id);
        $this->assertSame($project->name, $view->project->name);
        $this->assertSame($project->branch, $view->project->branch);
        $this->assertSame($project->allow_other_branch, $view->project->allow_other_branch);
        $this->assertSame($project->include_dev, $view->project->include_dev);
        $this->assertSame($project->servers->toJson(), $view->servers->toJson());
        $this->assertSame($project->channels->toJson(), $view->channels->toJson());
        $this->assertSame($project->heartbeats->toJson(), $view->heartbeats->toJson());
        $this->assertSame($project->sharedFiles->toJson(), $view->sharedFiles->toJson());
        $this->assertSame($project->configFiles->toJson(), $view->configFiles->toJson());
        $this->assertSame($project->checkUrls->toJson(), $view->checkUrls->toJson());
        $this->assertSame($project->variables->toJson(), $view->variables->toJson());
        $this->assertSame($optional->pluck('id')->toArray(), $view->optional->pluck('id')->toArray());
    }

    /**
     * @covers ::__construct
     * @covers ::project
     */
    public function testProjectReturnsErrorWhenInvalid()
    {
        $response = $this->get('/projects/9999');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
