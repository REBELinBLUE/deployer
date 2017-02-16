<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Resources;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\AuthenticatedTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Resources\ChannelController
 */
class ChannelControllerTest extends AuthenticatedTestCase
{
    use DatabaseMigrations;

    /**
     * @covers ::__construct
     * @covers ::store
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreCheckUrlRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testStore()
    {
        $this->markTestSkipped('Events are not working correctly when mocked!');
        $this->withoutEvents();

        factory(Project::class)->create();

        // FIXME: Need to test this with the various types!
        $input = [
            'name'                       => 'Notify me!',
            'project_id'                 => 1,
            'type'                       => 'custom',
            'url'                        => 'http://www.example.com',
            'on_deployment_success'      => true,
            'on_deployment_failure'      => false,
            'on_link_down'               => false,
            'on_link_still_down'         => false,
            'on_link_recovered'          => false,
            'on_heartbeat_missing'       => false,
            'on_heartbeat_still_missing' => false,
            'on_heartbeat_recovered'     => false,
        ];

        $output = array_merge([
            'id' => 1,
        ], array_except($input, ['url']));

        $response = $this->postJson('/notifications', $input);

        $response->assertStatus(Response::HTTP_CREATED)->assertJson($output);
        $this->assertDatabaseHas('channels', $output);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreCheckUrlRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testUpdate()
    {
        $this->markTestSkipped('Events are not working correctly when mocked!');
        $original = 'Notify Me!';
        $updated  = 'Notify You!';

        /** @var Channel $channel */
        $channel = factory(Channel::class)->create([
            'type'   => 'custom',
            'config' => ['url' => 'http://www.example.com'],
            'name'   => $original,
        ]);

        $data = array_only($channel->fresh()->toArray(), [
            'name'                      ,
            'type'                      ,
            'on_deployment_success'     ,
            'on_deployment_failure'     ,
            'on_link_down'              ,
            'on_link_still_down'        ,
            'on_link_recovered'         ,
            'on_heartbeat_missing'      ,
            'on_heartbeat_still_missing',
            'on_heartbeat_recovered'    ,
        ]);

        $input = array_merge($data, [
            'name' => $updated,
            'url'  => $channel->routeNotificationForWebhook(),
        ]);

        $response = $this->putJson('/channels/1', $input);

        $response->assertStatus(Response::HTTP_OK)->assertJson($input);
        $this->assertDatabaseHas('channels', ['name' => $updated]);
        $this->assertDatabaseMissing('channels', ['name' => $original]);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     */
    public function testUpdateReturnsErrorWhenInvalid()
    {
        $response = $this->putJson('/notifications/1000', [
            'name'                       => 'Notify me!',
            'type'                       => 'custom',
            'url'                        => 'http://www.example.com',
            'on_deployment_success'      => true,
            'on_deployment_failure'      => false,
            'on_link_down'               => false,
            'on_link_still_down'         => false,
            'on_link_recovered'          => false,
            'on_heartbeat_missing'       => false,
            'on_heartbeat_still_missing' => false,
            'on_heartbeat_recovered'     => false,
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDelete()
    {
        $this->markTestSkipped('Events are not working correctly when mocked!');
        $this->withoutEvents();

        $name = 'My Notification';

        factory(Channel::class)->create(['type' => 'custom', 'name' => $name]);

        $response = $this->deleteJson('/notifications/1');

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('channels', ['name' => $name, 'deleted_at' => null]);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDeleteReturnsErrorWhenInvalid()
    {
        $response = $this->deleteJson('/notifications/1000');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
