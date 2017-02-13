<?php

namespace REBELinBLUE\Deployer\Tests\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Events\UserWasCreated;
use REBELinBLUE\Deployer\Repositories\Contracts\UserRepositoryInterface;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Admin\UserController
 */
class UserControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $user = factory(User::class)->create([
            'email' => 'root@example.com',
        ]);

        $this->actingAs($user)->seeIsAuthenticated();
    }

    /**
     * @covers ::__construct
     * @covers ::index
     */
    public function testIndex()
    {
        $response = $this->get('/admin/users');

        $response->assertStatus(Response::HTTP_OK)->assertViewHas(['title', 'users']);

        /** @var \Robbo\Presenter\View\View $view */
        $view  = $response->getOriginalContent();
        $users = app(UserRepositoryInterface::class)->getAll();

        $this->assertSame($users->toJson(), $view->users);
    }

    /**
     * @covers ::__construct
     * @covers ::store
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreUserRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testStore()
    {
        $name     = 'John';
        $email    = 'admin@example.com';
        $password = 'a-random-password';

        $this->expectsEvents(UserWasCreated::class);

        $response = $this->postJson('/admin/users', [
            'name'                  => $name,
            'email'                 => $email,
            'password'              => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertStatus(Response::HTTP_OK)->assertJson(['id' => 2, 'name' => $name, 'email' => $email]);
        $this->assertDatabaseHas('users', ['id' => 2, 'name' => $name, 'email' => $email]);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreUserRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testUpdate()
    {
        $email    = 'admin@example.com';
        $original = 'John';
        $updated  = 'Paul';

        factory(User::class)->create(['name' => $original, 'email' => $email]);

        $response = $this->putJson('/admin/users/2', [
            'name'  => $updated,
            'email' => $email,
        ]);

        $response->assertStatus(Response::HTTP_OK)->assertJson(['id' => 2, 'name' => $updated, 'email' => $email]);
        $this->assertDatabaseHas('users', ['id' => 2, 'name' => $updated, 'email' => $email]);
        $this->assertDatabaseMissing('users', ['name' => $original, 'email' => $email]);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     */
    public function testUpdateReturnsErrorWhenInvalid()
    {
        $response = $this->putJson('/admin/users/1000', ['name' => 'Bob', 'email' => 'bob@example.com']);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
