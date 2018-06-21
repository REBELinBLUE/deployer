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

        $this->actingAs($user)->assertAuthenticated();

        factory(Project::class)->create([
            'hash' => 'a-fake-hash',
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
            'hash' => 'abcdefg123456',
        ]);

        factory(Server::class, 1)->create([
            'deploy_code' => false,
            'project_id'  => 1,
        ]);

        $this->doesntExpectJobs(QueueDeployment::class);

        $response = $this->postJson('/deploy/abcdefg123456');

        $response->assertStatus(Response::HTTP_OK)->assertExactJson(['success' => false]);

        $this->assertDatabaseMissing('deployments', ['project_id' => 1]);
    }

    /**
     * @dataProvider provideWebHookData
     * @covers ::__construct
     * @covers ::webhook
     * @covers ::parseWebhookRequest
     * @covers ::appendProjectSettings
     */
    public function testWebhookWithData($source, $file, $commit, $headers)
    {
        $this->withoutEvents();

        factory(Project::class)->create([
            'hash' => 'abcdefg123456',
        ]);

        factory(Server::class, 1)->create([
            'deploy_code' => true,
            'project_id'  => 1,
        ]);

        $this->expectsJobs(QueueDeployment::class);

        $data = json_decode(file_get_contents(__DIR__ . '/data/' . $file), true);

        $response = $this->postJson('/deploy/abcdefg123456', $data, $headers);

        $response->assertStatus(Response::HTTP_CREATED)->assertExactJson([
            'success'       => true,
            'deployment_id' => 1,
        ]);

        $this->assertDatabaseHas('deployments', [
            'project_id' => 1,
            'source'     => $source,
            'commit'     => $commit,
        ]);
    }

    public function provideWebHookData()
    {
        // FIXME: Add additional data types
        return [
            'Github' => [
                'Github',
                'github.json',
                'f99aa366c76589b69ef7cd3278e7f20d72b27127',
                ['X-GitHub-Delivery' => str_random(32), 'X-Github-Event' => 'push'],
            ],
        ];
    }
}
