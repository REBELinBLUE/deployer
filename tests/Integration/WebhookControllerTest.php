<?php

namespace REBELinBLUE\Deployer\Tests\Integration;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\WebhookController
 */
class WebhookControllerTest extends AuthenticatedTestCase
{
    use DatabaseMigrations;

    /**
     * @covers ::refresh
     */
    public function testRefresh()
    {
        factory(Project::class)->create([
            'hash' => 'a-fake-hash'
        ]);

        $response = $this->getJson('/webhook/1/refresh');

        $project = Project::find(1);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertExactJson(['url' => route('webhook.deploy', $project->hash)]);

        $this->assertDatabaseMissing('projects', ['hash' => 'a-fake-hash']);
    }
}
