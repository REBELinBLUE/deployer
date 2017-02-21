<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Resources;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\Integration\AuthenticatedTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Resources\ChannelController
 */
class ChannelControllerTest extends AuthenticatedTestCase
{
    use DatabaseMigrations;

    /**
     * @dataProvider provideChannelConfig
     * @covers ::__construct
     * @covers ::store
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreChannelRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testStore($type, $config)
    {
        $this->withoutEvents()->withoutNotifications();

        factory(Project::class)->create();

        $input = [
            'name'                       => 'Notify me!',
            'project_id'                 => 1,
            'type'                       => $type,
            'on_deployment_success'      => true,
            'on_deployment_failure'      => false,
            'on_link_down'               => false,
            'on_link_still_down'         => false,
            'on_link_recovered'          => false,
            'on_heartbeat_missing'       => false,
            'on_heartbeat_still_missing' => false,
            'on_heartbeat_recovered'     => false,
        ];

        $input = array_merge($input, $config);

        $output = array_merge([
            'id' => 1,
        ], array_except($input, array_keys($config)));

        $response = $this->postJson('/notifications', $input);

        $response->assertStatus(Response::HTTP_CREATED)->assertJson($output);
        $this->assertDatabaseHas('channels', $output);
    }

    public function provideChannelConfig()
    {
        return [
            ['custom', ['url' => 'http://www.example.com']],
            ['slack', ['channel' => '#deployer', 'icon' => ':ghost:', 'webhook' => 'http://hook.slack.com']],
            ['hipchat', ['room' => '#phpdeployment']],
            ['twilio', ['telephone' => '+4477089123456']],
            ['mail', ['email' => 'user@example.com']],
        ];
    }

    /**
     * @dataProvider provideChannelConfig
     * @covers ::__construct
     * @covers ::update
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreChannelRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testUpdate($type, $config)
    {
        $original = 'Notify Me!';
        $updated  = 'Notify You!';

        $this->withoutEvents()->withoutNotifications();

        /** @var Channel $channel */
        $channel = factory(Channel::class)->create([
            'type'   => $type,
            'config' => $config,
            'name'   => $original,
        ]);

        $data = array_only($channel->fresh()->toArray(), [
            'name',
            'type',
            'on_deployment_success',
            'on_deployment_failure',
            'on_link_down',
            'on_link_still_down',
            'on_link_recovered',
            'on_heartbeat_missing',
            'on_heartbeat_still_missing',
            'on_heartbeat_recovered',
        ]);

        $input = array_merge($data, [
            'name' => $updated,
        ], $config);

        $output = array_merge([
            'id' => 1,
        ], array_except($input, array_keys($config)));

        $response = $this->putJson('/notifications/1', $input);

        $response->assertStatus(Response::HTTP_OK)->assertJson($output);
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
        $this->withoutEvents()->withoutNotifications();

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
