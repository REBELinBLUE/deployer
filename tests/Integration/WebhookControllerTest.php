<?php

namespace REBELinBLUE\Deployer\Tests\Integration;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Jobs\QueueDeployment;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\WebhookController
 */
class WebhookControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @covers ::__construct
     * @covers ::refresh
     */
    public function testRefresh()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)->seeIsAuthenticated();

        factory(Project::class)->create([
            'hash' => 'a-fake-hash'
        ]);

        $response = $this->getJson('/webhook/1/refresh');

        $project = Project::find(1);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertExactJson(['url' => route('webhook.deploy', $project->hash)]);

        $this->assertDatabaseMissing('projects', ['hash' => 'a-fake-hash']);
    }

    /**
     * @covers ::__construct
     * @covers ::webhook
     */
    public function testWebhookWithNoServers()
    {
        factory(Project::class)->create([
            'hash' => 'abcdefg123456'
        ]);

        factory(Server::class, 1)->create([
            'deploy_code' => false,
            'project_id' => 1
        ]);

        $this->doesntExpectJobs(QueueDeployment::class);

        $response = $this->postJson('/deploy/abcdefg123456');

        $response->assertStatus(Response::HTTP_OK)->assertExactJson(['success' => false]);

        $this->assertDatabaseMissing('deployments', ['project_id' => 1]);
    }
}
