<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Resources;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\SharedFile;
use REBELinBLUE\Deployer\Tests\Integration\AuthenticatedTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Resources\SharedFilesController
 */
class SharedFilesControllerTest extends AuthenticatedTestCase
{
    use DatabaseMigrations;

    /**
     * @covers ::__construct
     * @covers ::store
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreSharedFileRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testStore()
    {
        factory(Project::class)->create();

        $input = [
            'name'        => 'Environment Config',
            'file'        => '.env',
            'target_type' => 'project',
            'target_id'   => 1,
        ];

        $output = array_merge([
            'id' => 1,
        ], $input);

        $this->postJson('/shared-files', $input)->assertStatus(Response::HTTP_CREATED)->assertJson($output);

        $this->assertDatabaseHas('shared_files', $output);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreSharedFileRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testUpdate()
    {
        $original = 'config.xml';
        $updated  = 'config.yml';

        /** @var SharedFile $file */
        $file = factory(SharedFile::class)->create(['name' => 'Config', 'file' => $original]);

        $data = array_only($file->fresh()->toArray(), [
            'name',
            'file',
        ]);

        $input = array_merge($data, [
            'name' => $updated,
        ]);

        $this->putJson('/shared-files/1', $input)->assertStatus(Response::HTTP_OK)->assertJson($input);

        $this->assertDatabaseHas('shared_files', ['name' => $updated]);
        $this->assertDatabaseMissing('shared_files', ['name' => $original]);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     */
    public function testUpdateReturnsErrorWhenInvalid()
    {
        $this->putJson('/shared-files/1000', [
            'name'       => 'Config',
            'file'       => '.env',
        ])->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDelete()
    {
        $file = 'config.yml';

        factory(SharedFile::class)->create(['file' => $file]);

        $this->deleteJson('/shared-files/1')->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('shared_files', ['file' => $file, 'deleted_at' => null]);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDeleteReturnsErrorWhenInvalid()
    {
        $this->deleteJson('/shared-files/1000')->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
