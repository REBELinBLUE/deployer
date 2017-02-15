<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Resources;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Tests\AuthenticatedTestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Resources\CheckUrlController
 */
class CheckUrlControllerTest extends AuthenticatedTestCase
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
        factory(Project::class)->create();

        $input = [
            'name'  => 'My site',
            'url'   => 'http://www.example.com',
            'period' => 30,
            'project_id' => 1
        ];

        $output = array_merge([
            'id' => 1
        ], $input);

        $response = $this->postJson('/check-url', $input);

        $response->assertStatus(Response::HTTP_CREATED)->assertJson($output);
        $this->assertDatabaseHas('checkurls', $output);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreUserRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testUpdate()
    {
        $original = 'My site';
        $updated  = 'Your site';

        /** @var CheckUrl $url */
        $url = factory(CheckUrl::class)->create(['name' => $original]);

        $data = array_only($url->fresh()->toArray(), [
            'name',
            'url',
            'period',
        ]);

        $input = array_merge($data, [
            'name' => $updated,
        ]);

        $response = $this->putJson('/check-url/1', $input);

        $response->assertStatus(Response::HTTP_OK)->assertJson($input);
        $this->assertDatabaseHas('checkurls', ['name' => $updated]);
        $this->assertDatabaseMissing('checkurls', ['name' => $original]);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     */
    public function testUpdateReturnsErrorWhenInvalid()
    {
        $response = $this->putJson('/check-url/1000', [
            'name' => 'My Site',
            'url' => 'http://www.example.com',
            'period' => 5
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDelete()
    {
        $name = 'My Site';

        factory(CheckUrl::class)->create(['name' => $name]);

        $response = $this->deleteJson('/check-url/1');

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('checkurls', ['name' => $name, 'deleted_at' => null]);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDeleteReturnsErrorWhenInvalid()
    {
        $response = $this->deleteJson('/check-url/1000');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
