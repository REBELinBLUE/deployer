<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Resources;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Jobs\TestServerConnection;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Tests\Integration\AuthenticatedTestCase;
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

        $this->postJson('/servers', $input)->assertStatus(Response::HTTP_CREATED)->assertJson($output);

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

        $this->putJson('/servers/1', $input)->assertStatus(Response::HTTP_OK)->assertJson($input);

        $this->assertDatabaseHas('servers', ['ip_address' => $updated]);
        $this->assertDatabaseMissing('servers', ['ip_address' => $original]);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     */
    public function testUpdateReturnsErrorWhenInvalid()
    {
        $this->putJson('/servers/1000', [
            'name'        => 'Web',
            'user'        => 'deploy',
            'ip_address'  => '127.0.0.1',
            'port'        => 22,
            'deploy_code' => true,
            'path'        => '/var/www',
        ])->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDelete()
    {
        $name = 'localhost';

        factory(Server::class)->create(['name' => $name]);

        $this->deleteJson('/servers/1')->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('servers', ['name' => $name, 'deleted_at' => null]);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDeleteReturnsErrorWhenInvalid()
    {
        $this->deleteJson('/servers/1000')->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers ::__construct
     * @covers ::test
     */
    public function testTest()
    {
        $this->withoutEvents()->expectsJobs(TestServerConnection::class);

        $name = 'localhost';

        factory(Server::class)->create(['name' => $name]);

        $this->postJson('/servers/1/test')->assertStatus(Response::HTTP_OK)->assertExactJson(['success' => true]);

        $this->assertDatabaseHas('servers', ['name' => $name, 'status' => Server::TESTING]);
    }

    /**
     * @covers ::__construct
     * @covers ::test
     */
    public function testTestReturnsErrorWhenInvalid()
    {
        $this->postJson('/servers/1000/test')->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers ::__construct
     * @covers ::reorder
     */
    public function testReorder()
    {
        $project = factory(Project::class)->create();

        factory(Server::class)->create(['name' => 'Localhost', 'order' => 0, 'project_id' => $project->id]);
        factory(Server::class)->create(['name' => 'Foo', 'order' => 2, 'project_id' => $project->id]);
        factory(Server::class)->create(['name' => 'Bar', 'order' => 1, 'project_id' => $project->id]);

        $this->postJson('/servers/reorder', ['servers' => [3, 1, 2]])
             ->assertStatus(Response::HTTP_OK)
             ->assertExactJson(['success' => true]);

        $this->assertDatabaseHas('servers', ['id' => 3, 'name' => 'Bar', 'order' => 0]);
        $this->assertDatabaseHas('servers', ['id' => 1, 'name' => 'Localhost', 'order' => 1]);
        $this->assertDatabaseHas('servers', ['id' => 2, 'name' => 'Foo', 'order' => 2]);
    }
}
