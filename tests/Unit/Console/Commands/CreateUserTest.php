<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Illuminate\Contracts\Validation\Factory;
use Mockery as m;
use REBELinBLUE\Deployer\Console\Commands\CreateUser;
use REBELinBLUE\Deployer\Events\UserWasCreated;
use REBELinBLUE\Deployer\Repositories\Contracts\UserRepositoryInterface;
use REBELinBLUE\Deployer\Services\Token\TokenGeneratorInterface;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;
use RuntimeException;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\CreateUser
 */
class CreateUserTest extends TestCase
{
    private $repository;

    private $validation;

    private $generator;

    private $console;

    public function setUp()
    {
        parent::setUp();

        // Can't use the real validator as it checks that the email doesn't exist in the DB
        $this->app->bind(Factory::class, function () {
            return $this->validation;
        });

        $console = m::mock(ConsoleApplication::class)->makePartial();
        $console->__construct();

        $this->repository = m::mock(UserRepositoryInterface::class);
        $this->generator  = m::mock(TokenGeneratorInterface::class);
        $this->validation = m::mock(Factory::class);

        $this->console = $console;
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
        $this->validation->shouldReceive('make->passes')->andReturn(true);

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
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'deployer:create-user',
            'name'    => $name,
            'email'   => $email,
        ]);

        $output = $tester->getDisplay();

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
        $this->validation->shouldReceive('make->passes')->andReturn(true);

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
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command'    => 'deployer:create-user',
            'name'       => $name,
            'email'      => $email,
            '--no-email' => true,
        ]);

        $output = $tester->getDisplay();

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
        $this->validation->shouldReceive('errors->first')->andReturnSelf();

        $this->expectException(RuntimeException::class);
        $this->doesntexpectEvents(UserWasCreated::class);

        $command = new CreateUser($this->repository, $this->generator);
        $command->setLaravel($this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command'  => 'deployer:create-user',
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
        ]);
    }
}
