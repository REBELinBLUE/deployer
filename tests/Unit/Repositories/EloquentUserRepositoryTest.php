<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Illuminate\Contracts\Hashing\Hasher;
use Mockery as m;
use REBELinBLUE\Deployer\Repositories\Contracts\UserRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentUserRepository;
use REBELinBLUE\Deployer\User;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentUserRepository
 */
class EloquentUserRepositoryTest extends EloquentRepositoryTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $this->assertExtendsEloquentRepository(User::class, EloquentUserRepository::class);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsUserRepositoryInterface()
    {
        $this->assertImplementsRepositoryInterface(
            User::class,
            EloquentUserRepository::class,
            UserRepositoryInterface::class
        );
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        $expected         = 'a-model';
        $expectedPassword = 'hashed-password';

        $fields   = ['foo' => 'bar', 'password' => 'password'];
        $create   = ['foo' => 'bar', 'password' => $expectedPassword];

        // Replace the hasher so that we can ensure the password is encrypted but that a known value is returned
        $mock = m::mock(Hasher::class);
        $mock->shouldReceive('make')->andReturn($expectedPassword);
        $this->app->instance('hash', $mock);

        $model = m::mock(User::class);
        $model->shouldReceive('create')->once()->with($create)->andReturn($expected);

        $repository = new EloquentUserRepository($model);
        $actual     = $repository->create($fields);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::updateById
     */
    public function testUpdateById()
    {
        $model_id     = 1;
        $fields       = ['foo' => 'bar'];

        $expected = m::mock(User::class);
        $expected->shouldReceive('update')->once()->with($fields);

        $model = m::mock(User::class);
        $model->shouldReceive('findOrFail')->once()->with($model_id)->andReturn($expected);

        $repository = new EloquentUserRepository($model);
        $actual     = $repository->updateById($fields, $model_id);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::updateById
     */
    public function testUpdateByIdRemovesBlankPassword()
    {
        $model_id     = 1;
        $fields       = ['foo' => 'bar', 'password' => ''];
        $update       = ['foo' => 'bar']; // This is what is expected to be passed to update

        $expected = m::mock(User::class);
        $expected->shouldReceive('update')->once()->with($update);

        $model = m::mock(User::class);
        $model->shouldReceive('findOrFail')->once()->with($model_id)->andReturn($expected);

        $repository = new EloquentUserRepository($model);
        $actual     = $repository->updateById($fields, $model_id);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::updateById
     */
    public function testUpdateByIdEncryptsPassword()
    {
        $model_id         = 1;
        $expectedPassword = 'a-hashed-password';
        $fields           = ['foo' => 'bar', 'password' => 'password'];
        $update           = ['foo' => 'bar', 'password' => $expectedPassword];

        $mock = m::mock(Hasher::class);
        $mock->shouldReceive('make')->andReturn($expectedPassword);
        $this->app->instance('hash', $mock);

        $expected = m::mock(User::class);
        $expected->shouldReceive('update')->once()->with($update);

        $model = m::mock(User::class);
        $model->shouldReceive('findOrFail')->once()->with($model_id)->andReturn($expected);

        $repository = new EloquentUserRepository($model);
        $actual     = $repository->updateById($fields, $model_id);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::findByEmailToken
     */
    public function testGetByEmailToken()
    {
        $token = 'an-email-token';

        $expected = m::mock(User::class);
        $expected->shouldReceive('first')->andReturnSelf();

        $model = m::mock(User::class);
        $model->shouldReceive('where')->once()->with('email_token', $token)->andReturn($expected);

        $repository = new EloquentUserRepository($model);
        $actual     = $repository->findByEmailToken($token);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::findByEmail
     */
    public function testGetByEmail()
    {
        $email = 'admin@example.com';

        $expected = m::mock(User::class);
        $expected->shouldReceive('first')->andReturnSelf();

        $model = m::mock(User::class);
        $model->shouldReceive('where')->once()->with('email', $email)->andReturn($expected);

        $repository = new EloquentUserRepository($model);
        $actual     = $repository->findByEmail($email);

        $this->assertSame($expected, $actual);
    }
}
