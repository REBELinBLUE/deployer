<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Response;
use REBELinBLUE\Deployer\Http\Requests\Request;
use REBELinBLUE\Deployer\Http\Requests\StoreServerTemplateRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\ServerTemplateRepositoryInterface;
use REBELinBLUE\Deployer\ServerTemplate as SrvTpl;
use REBELinBLUE\Deployer\Tests\Integration\AuthenticatedTestCase;

/**
 * Class ServerTemplateControllerTest
 * @package REBELinBLUE\Deployer\Tests\Integration\Admin
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Admin\ServerTemplateController
 */
class ServerTemplateControllerTest extends AuthenticatedTestCase
{
    use DatabaseMigrations;

    /**
     * @covers ::_construct
     * @covers ::index
     */
    public function testIndex()
    {
        $response = $this->get('/admin/servers');

        $response->assertStatus(Response::HTTP_OK)->assertViewHas(['title', 'servers']);

        $view = $response->getOriginalContent();
        $serverTemplates = $this->app->make(ServerTemplateRepositoryInterface::class)->getAll();

        $this->assertSame($serverTemplates->toJson(), $view->servers->toJson());
    }

    /**
     * @covers ::_construct
     * @covers ::create
     */
    public function testCreate()
    {
        $input = [
            'name' => 'a-server-template',
            'ip_address' => '127.0.0.1',
            'port' => 22
        ];

        $output = array_merge([
            'id' => 1
        ], $input);

        $this->postJson('/admin/servers', $input)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson($input);

        $this->assertDatabaseHas('server_templates', $output);
    }

    /**
     * @covers ::_construct
     * @covers ::create
     * @covers StoreServerTemplateRequest
     * @covers Request
     */
    public function testCreateValidateNameMissingFail()
    {
        $input = factory(SrvTpl::class)->make();
        unset($input->name);

        $this->postJson('/admin/servers', (array)$input)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['name']);
    }

    /**
     * @covers ::_construct
     * @covers ::create
     * @covers StoreServerTemplateRequest
     * @covers Request
     */
    public function testCreateValidateIpAddressMissingFail()
    {
        $input = factory(SrvTpl::class)->make();
        unset($input->ip_address);

        $this->postJson('/admin/servers', (array)$input)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['ip_address']);
    }

    /**
     * @covers ::_construct
     * @covers ::create
     * @covers StoreServerTemplateRequest
     * @covers Request
     */
    public function testCreateValidatePortMissingFail()
    {
        $input = factory(SrvTpl::class)->make();
        unset($input->port);

        $this->postJson('/admin/servers', (array)$input)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['port']);
    }

    /**
     * @covers ::_construct
     * @covers ::update
     */
    public function testUpdate()
    {
        /** @var SrvTpl $serverTemplate */
        $serverTemplate = factory(SrvTpl::class)->create(['name' => 'Foo']);

        $serverTemplate->name = 'Bar';

        $this->putJson('/admin/servers/1', $serverTemplate->toArray())
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(['id' => 1, 'name' => 'Bar']);

        $this->assertDatabaseHas('server_templates', ['id' => 1, 'name' => 'Bar']);
        $this->assertDatabaseMissing('server_templates', ['name' => 'Foo']);
    }

    /**
     * @covers ::_construct
     * @covers ::create
     * @covers StoreServerTemplateRequest
     * @covers Request
     */
    public function testUpdateReturnErrorWhenInvalidIpAddress()
    {
        $this->putJson('/admin/servers/1', ['ip_address' => '_invalid_ip_'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['ip_address']);
    }
}
