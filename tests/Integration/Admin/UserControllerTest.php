<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Events\UserWasCreated;
use REBELinBLUE\Deployer\Repositories\Contracts\UserRepositoryInterface;
use REBELinBLUE\Deployer\Tests\Integration\AuthenticatedTestCase;
use REBELinBLUE\Deployer\User;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Admin\UserController
 * @todo test validation
 */
class UserControllerTest extends AuthenticatedTestCase
{
    use DatabaseMigrations;

    /**
     * @covers ::__construct
     * @covers ::index
     */
    public function testIndex()
    {
        $response = $this->get('/admin/users');

        $response->assertStatus(Response::HTTP_OK)->assertViewHas(['title', 'users']);

        /** @var \McCool\LaravelAutoPresenter\BasePresenter $view */
        $view  = $response->getOriginalContent();
        $users = $this->app->make(UserRepositoryInterface::class)->getAll();

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
        $password = '3UltraS3cUr3!!';

        $this->expectsEvents(UserWasCreated::class);

        $this->postJson('/admin/users', [
            'name'                  => $name,
            'email'                 => $email,
            'password'              => $password,
            'password_confirmation' => $password,
        ])->assertStatus(Response::HTTP_CREATED)->assertJson(['id' => 2, 'name' => $name, 'email' => $email]);

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

        /** @var User $user */
        $user          = factory(User::class)->create(['name' => $original, 'email' => $email])->fresh();
        $original_hash = $user->password;

        $this->putJson('/admin/users/2', [
            'name'  => $updated,
            'email' => $email,
        ])->assertStatus(Response::HTTP_OK)->assertJson(['id' => 2, 'name' => $updated, 'email' => $email]);

        $this->assertDatabaseHas('users', ['id' => 2, 'name' => $updated, 'email' => $email]);
        $this->assertDatabaseMissing('users', ['name' => $original, 'email' => $email]);

        /** @var User $user */
        $user = $this->app->make(UserRepositoryInterface::class)->getById(2);

        $this->assertSame($original_hash, $user->password, 'Password has unexpectedly changed');
    }

    /**
     * @covers ::__construct
     * @covers ::update
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreUserRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testUpdateWithPassword()
    {
        $email    = 'admin@example.com';
        $original = 'John';
        $updated  = 'Paul';
        $password = 'a-random-password';

        /** @var User $user */
        $user          = factory(User::class)->create(['name' => $original, 'email' => $email])->fresh();
        $original_hash = $user->password;

        $this->putJson('/admin/users/2', [
            'name'                  => $updated,
            'email'                 => $email,
            'password'              => $password,
            'password_confirmation' => $password,
        ])->assertStatus(Response::HTTP_OK)->assertJson(['id' => 2, 'name' => $updated, 'email' => $email]);

        $this->assertDatabaseHas('users', ['id' => 2, 'name' => $updated, 'email' => $email]);
        $this->assertDatabaseMissing('users', ['name' => $original, 'email' => $email]);

        $user = $this->app->make(UserRepositoryInterface::class)->getById(2);

        /* @var User $user */
        $this->assertNotSame($original_hash, $user->password, 'Password has not been updated');
    }

    /**
     * @covers ::__construct
     * @covers ::update
     */
    public function testUpdateReturnsErrorWhenInvalid()
    {
        $this->putJson('/admin/users/1000', ['name' => 'Bob', 'email' => 'bob@example.com'])
             ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDelete()
    {
        $email = 'admin@example.com';
        $name  = 'John';

        factory(User::class)->create(['name' => $name, 'email' => $email]);

        $this->deleteJson('/admin/users/2')->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('users', ['name' => $name, 'email' => $email, 'deleted_at' => null]);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDeleteReturnsErrorWhenInvalid()
    {
        $this->deleteJson('/admin/users/1000')->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
