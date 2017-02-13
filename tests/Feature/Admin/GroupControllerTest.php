<?php

namespace REBELinBLUE\Deployer\Tests\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Response;
use REBELinBLUE\Deployer\Group;
use REBELinBLUE\Deployer\Repositories\Contracts\GroupRepositoryInterface;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Admin\GroupController
 */
class GroupControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $user = factory(User::class)->create();

        $this->actingAs($user)
             ->seeIsAuthenticated();
    }

    /**
     * @covers ::index
     */
    public function testIndex()
    {
        //$groups = app(GroupRepositoryInterface::class)->getAll();

        $response = $this->get('/admin/groups');

        $response->assertStatus(Response::HTTP_OK)
                 ->assertViewHas(['title', 'groups']);
    }

    /**
     * @covers ::store
     */
    public function testStoreCreatesGroup()
    {
        $expected = 'a-new-group';

        $response = $this->postJson('/admin/groups', ['name' => $expected]);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson(['name' => $expected]);

        $this->assertDatabaseHas('groups', ['name' => $expected]);
    }

    /**
     * @covers ::store
     */
    public function testStoreValidates()
    {
        $response = $this->postJson('/admin/groups', ['foo' => 'bar', 'name' => '']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonStructure(['name']);
    }

    /**
     * @covers ::update
     */
    public function testUpdateReturnsErrorWhenInvalid()
    {
        $response = $this->putJson('/admin/groups/1000', ['name' => 'Bar']);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers ::update
     */
    public function testUpdateUpdatesGroup()
    {
        factory(Group::class)->create(['name' => 'Foo']);

        $response = $this->putJson('/admin/groups/2', ['name' => 'Bar']);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'id'   => 2,
                     'name' => 'Bar',
                 ]);

        $this->assertDatabaseHas('groups', ['name' => 'Bar']);
        $this->assertDatabaseMissing('groups', ['name' => 'Foo']);
    }

    /**
     * @covers ::update
     */
    public function testUpdateValidates()
    {
        factory(Group::class)->create(['name' => 'Foo']);

        $response = $this->putJson('/admin/groups/2', ['foo' => 'bar', 'name' => '']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonStructure(['name']);
    }
}
