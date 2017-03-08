<?php

namespace REBELinBLUE\Deployer\Tests\Integration;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Jobs\AbortDeployment;
use REBELinBLUE\Deployer\Jobs\QueueUpdateGitMirror;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\ServerLog;
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

        // FIXME: Test 404

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
    public function testLog()
    {
        $this->withoutEvents();

        // FIXME: mock decorator and test

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
    public function testLogReturnsErrorWhenInvalid()
    {
        $this->withoutEvents();

        $response = $this->getJson('/log/9999');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
