<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Resources;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Resources\HeartbeatController
 */
class HeartbeatControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @covers ::__construct
     * @covers ::ping
     */
    public function testPing()
    {
        /** @var Heartbeat $heartbeat */
        $heartbeat = factory(Heartbeat::class)->create()->fresh();

        $now = Carbon::create(2017, 1, 2, 15, 15, 0, 'UTC');
        Carbon::setTestNow($now);

        $response = $this->dontSeeIsAuthenticated()->getJson('/heartbeat/' . $heartbeat->hash);

        $response->assertStatus(Response::HTTP_OK)->assertExactJson(['success' => true]);

        $this->assertDatabaseHas('heartbeats', ['id' => 1, 'last_activity' => $now, 'status' => Heartbeat::OK]);
    }
}
