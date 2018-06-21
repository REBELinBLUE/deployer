<?php

namespace REBELinBLUE\Deployer\tests\Integration;

use Carbon\Carbon;
use DOMDocument;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test for end-points which are webhooks which don't require logging in.
 */
class WebhooksTest extends TestCase
{
    // FIXME: Move these to the actual controllers
    use DatabaseMigrations;

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\DashboardController::__construct
     * @covers \REBELinBLUE\Deployer\Http\Controllers\DashboardController::cctray
     */
    public function testCctray()
    {
        factory(Project::class)->create(['status' => Project::NOT_DEPLOYED]);

        $date    = Carbon::create(2016, 10, 12, 19, 56, 00, 'UTC');
        $project = factory(Project::class)->create([
            'status'   => Project::FINISHED,
            'last_run' => $date,
        ]);

        factory(Deployment::class)->create([
            'project_id' => $project->id,
            'status'     => Deployment::COMPLETED,
        ]);

        $response = $this->assertGuest()->get('/cctray.xml', ['Accept' => 'application/xml']);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertHeader('Content-Type', 'application/xml');

        $expected = new DOMDocument();
        $expected->loadXML('<Projects><Project/><Project/></Projects>');

        $actual = new DOMDocument();
        $actual->loadXML($response->getContent());

        // FIXME: Change this to also care about attributes!
        $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\HeartbeatController::__construct
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\HeartbeatController::ping
     */
    public function testPing()
    {
        /** @var Heartbeat $heartbeat */
        $heartbeat = factory(Heartbeat::class)->create()->fresh();

        $now = Carbon::create(2017, 1, 2, 15, 15, 0, 'UTC');
        Carbon::setTestNow($now);

        $this->assertGuest()
             ->getJson('/heartbeat/' . $heartbeat->hash)
             ->assertStatus(Response::HTTP_OK)
             ->assertExactJson(['success' => true]);

        $this->assertDatabaseHas('heartbeats', ['id' => 1, 'last_activity' => $now, 'status' => Heartbeat::OK]);
    }
}
