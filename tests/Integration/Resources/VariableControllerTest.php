<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Resources;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\Integration\AuthenticatedTestCase;
use REBELinBLUE\Deployer\Variable;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Resources\VariableController
 */
class VariableControllerTest extends AuthenticatedTestCase
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
        factory(Project::class)->create();

        $input = [
            'name'        => 'SYMFONY_ENV',
            'value'       => 'prod',
            'target_type' => 'project',
            'target_id'   => 1,
        ];

        $output = array_merge([
            'id' => 1,
        ], $input);

        $this->postJson('/variables', $input)->assertStatus(Response::HTTP_CREATED)->assertJson($output);

        $this->assertDatabaseHas('variables', $output);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreVariableRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testUpdate()
    {
        $original = 'prod';
        $updated  = 'dev';

        /** @var Variable $variable */
        $variable = factory(Variable::class)->create(['name' => 'SYMFONY_ENV', 'value' => $original]);

        $data = array_only($variable->fresh()->toArray(), [
            'name',
            'value',
        ]);

        $input = array_merge($data, [
            'name' => $updated,
        ]);

        $this->putJson('/variables/1', $input)->assertStatus(Response::HTTP_OK)->assertJson($input);

        $this->assertDatabaseHas('variables', ['name' => $updated]);
        $this->assertDatabaseMissing('variables', ['name' => $original]);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     */
    public function testUpdateReturnsErrorWhenInvalid()
    {
        $response = $this->putJson('/variables/1000', [
            'name'       => 'SYMFONY_ENV',
            'value'      => 'prod',
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDelete()
    {
        $name = 'SYMFONY_ENV';

        factory(Variable::class)->create(['name' => $name]);

        $this->deleteJson('/variables/1')->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('variables', ['name' => $name, 'deleted_at' => null]);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDeleteReturnsErrorWhenInvalid()
    {
        $this->deleteJson('/variables/1000')->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
