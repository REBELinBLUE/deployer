<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Template;
use REBELinBLUE\Deployer\Tests\AuthenticatedTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Admin\TemplateController
 */
class TemplateControllerTest extends AuthenticatedTestCase
{
    use DatabaseMigrations;

    /**
     * @covers ::__construct
     * @covers ::store
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreTemplateRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testStore()
    {
        $expected = 'a-new-template';

        $response = $this->postJson('/admin/templates', ['name' => $expected]);

        $response->assertStatus(Response::HTTP_CREATED)->assertJson(['name' => $expected]);
        $this->assertDatabaseHas('templates', ['name' => $expected]);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreTemplateRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testUpdate()
    {
        factory(Template::class)->create(['name' => 'Foo']);

        $response = $this->putJson('/admin/templates/1', ['name' => 'Bar']);

        $response->assertStatus(Response::HTTP_OK)->assertJson(['id' => 1, 'name' => 'Bar']);
        $this->assertDatabaseHas('templates', ['id' => 1, 'name' => 'Bar']);
        $this->assertDatabaseMissing('templates', ['name' => 'Foo']);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     */
    public function testUpdateReturnsErrorWhenInvalid()
    {
        $response = $this->putJson('/admin/templates/1000', ['name' => 'Bar']);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDelete()
    {
        $this->markTestSkipped('not yet working');

        $name  = 'Foo';

        factory(Template::class)->create(['name' => $name]);

        $response = $this->deleteJson('/admin/templates/1');

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('templates', ['name' => $name, 'deleted_at' => null]);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDeleteReturnsErrorWhenInvalid()
    {
        $response = $this->deleteJson('/admin/templates/1000');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
