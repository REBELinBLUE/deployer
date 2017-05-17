<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Response;
use REBELinBLUE\Deployer\Repositories\Contracts\ServerTemplateRepositoryInterface;
use REBELinBLUE\Deployer\ServerTemplate;
use REBELinBLUE\Deployer\Tests\Integration\AuthenticatedTestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Admin\ServerTemplateController
 */
class ServerTemplateControllerTest extends AuthenticatedTestCase
{
    use DatabaseMigrations;

    /**
     * @covers ::__construct
     * @covers ::index
     */
    public function testIndex()
    {
        $response = $this->get('/admin/servers');

        $response->assertStatus(Response::HTTP_OK)->assertViewHas(['title', 'servers']);

        $view            = $response->getOriginalContent();
        $serverTemplates = $this->app->make(ServerTemplateRepositoryInterface::class)->getAll();

        $this->assertSame($serverTemplates->toJson(), $view->servers->toJson());
    }

    /**
     * @covers ::__construct
     * @covers ::store
     */
    public function testCreate()
    {
        $input = [
            'name'       => 'a-server-template',
            'ip_address' => '127.0.0.1',
            'port'       => 22,
        ];

        $output = array_merge([
            'id' => 1,
        ], $input);

        $this->postJson('/admin/servers', $input)
             ->assertStatus(Response::HTTP_CREATED)
             ->assertJson($input);

        $this->assertDatabaseHas('server_templates', $output);
    }

    /**
     * @covers ::__construct
     * @covers ::store
     */
    public function testCreateValidateNameMissingFail()
    {
        $input = factory(ServerTemplate::class)->make();
        unset($input->name);

        $this->postJson('/admin/servers', (array) $input)
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJsonStructure(['name']);
    }

    /**
     * @covers ::__construct
     * @covers ::store
     */
    public function testCreateValidateIpAddressMissingFail()
    {
        $input = factory(ServerTemplate::class)->make();
        unset($input->ip_address);

        $this->postJson('/admin/servers', (array) $input)
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJsonStructure(['ip_address']);
    }

    /**
     * @covers ::__construct
     * @covers ::store
     */
    public function testCreateValidatePortMissingFail()
    {
        $input = factory(ServerTemplate::class)->make();
        unset($input->port);

        $this->postJson('/admin/servers', (array) $input)
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJsonStructure(['port']);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     */
    public function testUpdate()
    {
        /** @var ServerTemplate $serverTemplate */
        $serverTemplate = factory(ServerTemplate::class)->create(['name' => 'Foo']);

        $serverTemplate->name = 'Bar';

        $this->putJson('/admin/servers/1', $serverTemplate->toArray())
             ->assertStatus(Response::HTTP_OK)
             ->assertJson(['id' => 1, 'name' => 'Bar']);

        $this->assertDatabaseHas('server_templates', ['id' => 1, 'name' => 'Bar']);
        $this->assertDatabaseMissing('server_templates', ['name' => 'Foo']);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     */
    public function testUpdateReturnErrorWhenInvalidIpAddress()
    {
        $this->putJson('/admin/servers/1', ['ip_address' => '_invalid_ip_'])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJsonStructure(['ip_address']);
    }
}
