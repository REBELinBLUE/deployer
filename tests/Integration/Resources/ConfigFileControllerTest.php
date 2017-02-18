<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Resources;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use REBELinBLUE\Deployer\ConfigFile;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\Integration\AuthenticatedTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Resources\ConfigFileController
 */
class ConfigFileControllerTest extends AuthenticatedTestCase
{
    use DatabaseTransactions;

    /**
     * @cover ::__construct
     * @covers ::store
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreConfigFileRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testStore()
    {
        factory(Project::class)->create();

        $input = [
            'name'        => 'Environment Config',
            'path'        => '.env',
            'content'     => 'lorem ipsum',
            'target_type' => 'project',
            'target_id'   => 1,
        ];

        $output = array_merge([
            'id' => 1,
        ], $input);

        $this->postJson('/config-file', $input)->assertStatus(Response::HTTP_CREATED)->assertJson($output);

        $this->assertDatabaseHas('config_files', $output);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreConfigFileRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testUpdate()
    {
        $original = 'config.xml';
        $updated  = 'config.yml';

        /** @var ConfigFile $file */
        $file = factory(ConfigFile::class)->create(['path' => $original]);

        $data = array_only($file->fresh()->toArray(), [
            'name',
            'path',
            'content',
        ]);

        $input = array_merge($data, [
            'path' => $updated,
        ]);

        $this->putJson('/config-file/1', $input)->assertStatus(Response::HTTP_OK)->assertJson($input);

        $this->assertDatabaseHas('config_files', ['path' => $updated]);
        $this->assertDatabaseMissing('config_files', ['path' => $original]);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     */
    public function testUpdateReturnsErrorWhenInvalid()
    {
        $this->putJson('/config-file/1000', [
            'name'    => 'Config',
            'path'    => '.env',
            'content' => 'lorem ipsum',
        ])->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDelete()
    {
        $file = 'config.yml';

        factory(ConfigFile::class)->create(['path' => $file]);

        $this->deleteJson('/config-file/1')->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('config_files', ['path' => $file, 'deleted_at' => null]);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDeleteReturnsErrorWhenInvalid()
    {
        $this->deleteJson('/config-file/1000')->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
