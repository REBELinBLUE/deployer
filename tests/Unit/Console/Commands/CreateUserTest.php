<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Illuminate\Contracts\Validation\Factory;
use Mockery as m;
use REBELinBLUE\Deployer\Console\Commands\CreateUser;
use REBELinBLUE\Deployer\Events\UserWasCreated;
use REBELinBLUE\Deployer\Repositories\Contracts\UserRepositoryInterface;
use REBELinBLUE\Deployer\Services\Token\TokenGeneratorInterface;
use REBELinBLUE\Deployer\User;
use RuntimeException;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\CreateUser
 */
class CreateUserTest extends CommandTestCase
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * @var Factory
     */
    private $validation;

    /**
     * @var TokenGeneratorInterface
     */
    private $generator;

    public function setUp()
    {
        parent::setUp();

        $this->repository = m::mock(UserRepositoryInterface::class);
        $this->generator  = m::mock(TokenGeneratorInterface::class);
        $this->validation = m::mock(Factory::class);

        // Can't use the real validator as it checks that the email doesn't exist in the DB
        $this->app->bind(Factory::class, function () {
            return $this->validation;
        });
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandle()
    {
        $name     = 'Jill';
        $email    = 'jill@example.com';
        $password = 'qwErtY1$R';
        $this->generator->shouldReceive('generateRandom')->with(15)->andReturn($password);
        $this->validation->shouldReceive('make')->andReturnSelf();
        $this->validation->shouldReceive('passes')->andReturn(true);

        $user = m::mock(User::class);
        $user->shouldReceive('getAttribute')->with('email')->andReturn($email);

        $this->repository->shouldReceive('create')->with([
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
        ])->andReturn($user);

        $this->expectsEvents(UserWasCreated::class);

        $command = new CreateUser($this->repository, $this->generator);
        $command->setLaravel($this->app);

        $output = $this->runCommand($command, ['name' => $name, 'email' => $email]);

        $this->assertContains($email, $output);
        $this->assertNotContains($password, $output);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleShouldNotSendEmail()
    {
        $name     = 'Jill';
        $email    = 'jill@example.com';
        $password = 'qwErtY1$R';
        $this->generator->shouldReceive('generateRandom')->with(15)->andReturn($password);
        $this->validation->shouldReceive('make')->andReturnSelf();
        $this->validation->shouldReceive('passes')->andReturn(true);

        $user = m::mock(User::class);
        $user->shouldReceive('getAttribute')->with('email')->andReturn($email);

        $this->repository->shouldReceive('create')->with([
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
        ])->andReturn($user);

        $this->doesntexpectEvents(UserWasCreated::class);

        $command = new CreateUser($this->repository, $this->generator);
        $command->setLaravel($this->app);

        $output = $this->runCommand($command, ['name' => $name, 'email' => $email, '--no-email' => true]);

        $this->assertNotContains($email, $output);
        $this->assertContains($password, $output);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleThrowsExceptionOnValidationError()
    {
        $name     = 'Jill';
        $email    = 'not-a-valid-email-address';
        $password = 'a';

        $this->validation->shouldReceive('make')->andReturnSelf();
        $this->validation->shouldReceive('passes')->andReturn(false);
        $this->validation->shouldReceive('errors')->andReturnSelf();
        $this->validation->shouldReceive('first')->andReturnSelf();

        $this->expectException(RuntimeException::class);
        $this->doesntexpectEvents(UserWasCreated::class);

        $command = new CreateUser($this->repository, $this->generator);
        $command->setLaravel($this->app);

        $this->runCommand($command, ['name' => $name, 'email' => $email, 'password' => $password]);
    }
}
