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

        $this->actingAs($user)->seeIsAuthenticated();
    }

    /**
     * @covers ::__construct
     * @covers ::index
     */
    public function testIndex()
    {
        // FIXME: How do I test this?
        //$groups = app(GroupRepositoryInterface::class)->getAll();

        $response = $this->get('/admin/groups');

        $response->assertStatus(Response::HTTP_OK)->assertViewHas(['title', 'groups']);
    }

    /**
     * @covers ::__construct
     * @covers ::store
     */
    public function testStoreCreatesGroup()
    {
        $expected = 'a-new-group';

        $response = $this->postJson('/admin/groups', ['name' => $expected]);

        $response->assertStatus(Response::HTTP_OK)->assertJson(['name' => $expected]);

        $this->assertDatabaseHas('groups', ['name' => $expected]);
    }

    /**
     * @covers ::__construct
     * @covers ::store
     */
    public function testStoreValidates()
    {
        $response = $this->postJson('/admin/groups', ['foo' => 'bar', 'name' => '']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonStructure(['name']);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     */
    public function testUpdateReturnsErrorWhenInvalid()
    {
        $response = $this->putJson('/admin/groups/1000', ['name' => 'Bar']);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     */
    public function testUpdateUpdatesGroup()
    {
        factory(Group::class)->create(['name' => 'Foo']);

        $response = $this->putJson('/admin/groups/2', ['name' => 'Bar']);

        $response->assertStatus(Response::HTTP_OK)->assertJson(['id' => 2, 'name' => 'Bar']);

        $this->assertDatabaseHas('groups', ['name' => 'Bar']);
        $this->assertDatabaseMissing('groups', ['name' => 'Foo']);
    }

    /**
 * @covers ::__construct
 * @covers ::update
 */
    public function testUpdateValidates()
    {
        factory(Group::class)->create(['name' => 'Foo']);

        $response = $this->putJson('/admin/groups/2', ['foo' => 'bar', 'name' => '']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonStructure(['name']);
    }

    /**
     * @covers ::__construct
     * @covers ::reorder
     */
    public function testReorder()
    {
        factory(Group::class)->create(['name' => 'Foo', 'order' => 2]);
        factory(Group::class)->create(['name' => 'Bar', 'order' => 1]);

        $response = $this->post('/admin/groups/reorder', ['groups' => [3, 1, 2]]);

        $response->assertStatus(Response::HTTP_OK)->assertExactJson(['success' => true]);

        $this->assertDatabaseHas('groups', ['name' => 'Bar', 'order' => 0]);
        $this->assertDatabaseHas('groups', ['name' => 'Projects', 'order' => 1]);
        $this->assertDatabaseHas('groups', ['name' => 'Foo', 'order' => 2]);
    }
}
