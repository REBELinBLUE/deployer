<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Resources;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Tests\AuthenticatedTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Resources\ServerController
 */
class ServerControllerTest extends AuthenticatedTestCase
{
    use DatabaseMigrations;

    /**
     * @covers ::__construct
     * @covers ::store
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreServerRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testStore()
    {
        factory(Project::class)->create();

        $input = [
            'name'        => 'Web',
            'user'        => 'deploy',
            'ip_address'  => '127.0.0.1',
            'port'        => 22,
            'deploy_code' => true,
            'path'        => '/var/www',
            'project_id'  => 1,
        ];

        $output = array_merge([
            'id' => 1,
        ], $input);

        $response = $this->postJson('/servers', $input);

        $response->assertStatus(Response::HTTP_CREATED)->assertJson($output);
        $this->assertDatabaseHas('servers', $output);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreServerRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testUpdate()
    {
        $original = '126.0.0.1';
        $updated  = '127.0.0.1';

        /** @var Server $server */
        $server = factory(Server::class)->create(['ip_address' => $original]);

        $data = array_only($server->fresh()->toArray(), [
            'name',
            'user',
            'ip_address',
            'port',
            'path',
            'deploy_code',
        ]);

        $input = array_merge($data, [
            'ip_address' => $updated,
        ]);

        $response = $this->putJson('/servers/1', $input);

        $response->assertStatus(Response::HTTP_OK)->assertJson($input);
        $this->assertDatabaseHas('servers', ['ip_address' => $updated]);
        $this->assertDatabaseMissing('servers', ['ip_address' => $original]);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     */
    public function testUpdateReturnsErrorWhenInvalid()
    {
        $response = $this->putJson('/servers/1000', [
            'name'        => 'Web',
            'user'        => 'deploy',
            'ip_address'  => '127.0.0.1',
            'port'        => 22,
            'deploy_code' => true,
            'path'        => '/var/www',
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDelete()
    {
        $name = 'localhost';

        factory(Server::class)->create(['name' => $name]);

        $response = $this->deleteJson('/servers/1');

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('servers', ['name' => $name, 'deleted_at' => null]);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDeleteReturnsErrorWhenInvalid()
    {
        $response = $this->deleteJson('/servers/1000');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
