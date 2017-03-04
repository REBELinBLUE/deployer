<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Response;
use REBELinBLUE\Deployer\Group;
use REBELinBLUE\Deployer\Repositories\Contracts\GroupRepositoryInterface;
use REBELinBLUE\Deployer\Tests\Integration\AuthenticatedTestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Admin\GroupController
 */
class GroupControllerTest extends AuthenticatedTestCase
{
    use DatabaseMigrations;

    /**
     * @covers ::__construct
     * @covers ::index
     */
    public function testIndex()
    {
        $response = $this->get('/admin/groups');

        $response->assertStatus(Response::HTTP_OK)->assertViewHas(['title', 'groups']);

        /** @var \McCool\LaravelAutoPresenter\BasePresenter $view */
        $view   = $response->getOriginalContent();
        $groups = $this->app->make(GroupRepositoryInterface::class)->getAll();

        $this->assertSame($groups->toJson(), $view->groups->toJson());
    }

    /**
     * @covers ::__construct
     * @covers ::store
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreGroupRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testStore()
    {
        $expected = 'a-new-group';

        $this->postJson('/admin/groups', ['name' => $expected])
             ->assertStatus(Response::HTTP_CREATED)
             ->assertJson(['name' => $expected]);

        $this->assertDatabaseHas('groups', ['name' => $expected]);
    }

    /**
     * @covers ::__construct
     * @covers ::store
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreGroupRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testStoreValidatesNameRequired()
    {
        $this->postJson('/admin/groups', ['foo' => 'bar', 'name' => ''])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJsonStructure(['name']);
    }

    /**
     * @covers ::__construct
     * @covers ::store
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreGroupRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testStoreValidatesNameUnique()
    {
        factory(Group::class)->create(['name' => 'Foo']);

        $this->postJson('/admin/groups', ['name' => 'Foo'])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJsonStructure(['name']);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreGroupRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testUpdate()
    {
        factory(Group::class)->create(['name' => 'Foo']);

        $this->putJson('/admin/groups/2', ['name' => 'Bar'])
             ->assertStatus(Response::HTTP_OK)
             ->assertJson(['id' => 2, 'name' => 'Bar']);

        $this->assertDatabaseHas('groups', ['id' => 2, 'name' => 'Bar']);
        $this->assertDatabaseMissing('groups', ['name' => 'Foo']);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     */
    public function testUpdateReturnsErrorWhenInvalid()
    {
        $this->putJson('/admin/groups/1000', ['name' => 'Bar'])->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreGroupRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testUpdateDoesNotErrorIfNameIsNotChanged()
    {
        factory(Group::class)->create(['name' => 'Foo']);

        $this->putJson('/admin/groups/2', ['name' => 'Foo'])
             ->assertStatus(Response::HTTP_OK)
             ->assertJson(['id' => 2, 'name' => 'Foo']);

        $this->assertDatabaseHas('groups', ['id' => 2, 'name' => 'Foo']);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreGroupRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testUpdateValidateNameRequired()
    {
        factory(Group::class)->create(['name' => 'Foo']);

        $this->putJson('/admin/groups/2', ['foo' => 'bar', 'name' => ''])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJsonStructure(['name']);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreGroupRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testUpdateValidatesNameUnique()
    {
        factory(Group::class)->create(['name' => 'Foo']);
        factory(Group::class)->create(['name' => 'Bar']);

        $this->putJson('/admin/groups/2', ['name' => 'Bar'])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJsonStructure(['name']);
    }

    /**
     * @covers ::__construct
     * @covers ::reorder
     */
    public function testReorder()
    {
        factory(Group::class)->create(['name' => 'Foo', 'order' => 2]);
        factory(Group::class)->create(['name' => 'Bar', 'order' => 1]);

        $this->postJson('/admin/groups/reorder', ['groups' => [3, 1, 2]])
             ->assertStatus(Response::HTTP_OK)
             ->assertExactJson(['success' => true]);

        $this->assertDatabaseHas('groups', ['id' => 3, 'name' => 'Bar', 'order' => 0]);
        $this->assertDatabaseHas('groups', ['id' => 1, 'name' => 'Projects', 'order' => 1]);
        $this->assertDatabaseHas('groups', ['id' => 2, 'name' => 'Foo', 'order' => 2]);
    }
}
