<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Resources;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Tests\AuthenticatedTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Resources\CommandController
 */
class CommandControllerTest extends AuthenticatedTestCase
{
    use DatabaseMigrations;

    /**
     * @covers ::__construct
     * @covers ::store
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreVariableRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testStore()
    {
        factory(Server::class)->create();

        $input = [
            'name'        => 'My command!',
            'user'        => 'root',
            'target_type' => 'project',
            'target_id'   => 1,
            'script'      => 'ls -la',
            'step'        => Command::BEFORE_INSTALL,
            'optional'    => false,
            'default_on'  => true,
            'servers'     => [1],
        ];

        $output = array_merge([
            'id' => 1,
        ], array_except($input, ['servers']));

        $this->postJson('/commands', $input)->assertStatus(Response::HTTP_CREATED)->assertJson($output);

        $this->assertDatabaseHas('commands', $output);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreVariableRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testUpdate()
    {
        $original = 'root';
        $updated  = 'deploy';

        /** @var Command $command */
        $command = factory(Command::class)->create(['user' => $original]);

        $data = array_only($command->fresh()->toArray(), [
            'name',
            'user',
            'script',
            'optional',
            'default_on',
            'servers',
        ]);

        $input = array_merge($data, [
            'user' => $updated,
        ]);

        $response = $this->putJson('/commands/1', $input);

        $response->assertStatus(Response::HTTP_OK)->assertJson($input);
        $this->assertDatabaseHas('commands', ['user' => $updated]);
        $this->assertDatabaseMissing('commands', ['user' => $original]);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     */
    public function testUpdateReturnsErrorWhenInvalid()
    {
        $this->putJson('/commands/1000', [
            'name'        => 'My command!',
            'user'        => 'root',
            'script'      => 'ls -la',
            'optional'    => false,
            'default_on'  => true,
            'servers'     => [1],
        ])->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDelete()
    {
        $name = 'My Command!';

        factory(Command::class)->create(['name' => $name]);

        $response = $this->deleteJson('/commands/1');

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('commands', ['name' => $name, 'deleted_at' => null]);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDeleteReturnsErrorWhenInvalid()
    {
        $this->deleteJson('/commands/1000')->assertStatus(Response::HTTP_NOT_FOUND);
    }

//
//    /**
//     * @covers ::__construct
//     * @covers ::reorder
//     */
//    public function testReorder()
//    {
//        factory(Group::class)->create(['name' => 'Foo', 'order' => 2]);
//        factory(Group::class)->create(['name' => 'Bar', 'order' => 1]);
//
//        $response = $this->post('/admin/groups/reorder', ['groups' => [3, 1, 2]]);
//
//        $response->assertStatus(Response::HTTP_OK)->assertExactJson(['success' => true]);
//        $this->assertDatabaseHas('groups', ['id' => 3, 'name' => 'Bar', 'order' => 0]);
//        $this->assertDatabaseHas('groups', ['id' => 1, 'name' => 'Projects', 'order' => 1]);
//        $this->assertDatabaseHas('groups', ['id' => 2, 'name' => 'Foo', 'order' => 2]);
//    }
}
