<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Resources;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\Integration\AuthenticatedTestCase;
use Symfony\Component\HttpFoundation\Response;

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
            'name'       => 'My site',
            'url'        => 'http://www.example.com',
            'period'     => 30,
            'project_id' => 1,
        ];

        $output = array_merge([
            'id' => 1,
        ], $input);

        $this->postJson('/check-urls', $input)->assertStatus(Response::HTTP_CREATED)->assertJson($output);

        $this->assertDatabaseHas('check_urls', $output);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreCheckUrlRequest
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

        $this->putJson('/check-urls/1', $input)->assertStatus(Response::HTTP_OK)->assertJson($input);

        $this->assertDatabaseHas('check_urls', ['name' => $updated]);
        $this->assertDatabaseMissing('check_urls', ['name' => $original]);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     */
    public function testUpdateReturnsErrorWhenInvalid()
    {
        $this->putJson('/check-urls/1000', [
            'name'   => 'My Site',
            'url'    => 'http://www.example.com',
            'period' => 5,
        ])->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDelete()
    {
        $name = 'My Site';

        factory(CheckUrl::class)->create(['name' => $name]);

        $this->deleteJson('/check-urls/1')->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('check_urls', ['name' => $name, 'deleted_at' => null]);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDeleteReturnsErrorWhenInvalid()
    {
        $this->deleteJson('/check-urls/1000')->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
