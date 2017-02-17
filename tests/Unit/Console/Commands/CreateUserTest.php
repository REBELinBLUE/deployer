<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Foundation\Application;
use Mockery as m;
use REBELinBLUE\Deployer\Console\Commands\CreateUser;
use REBELinBLUE\Deployer\Events\UserWasCreated;
use REBELinBLUE\Deployer\Repositories\Contracts\UserRepositoryInterface;
use REBELinBLUE\Deployer\Services\Token\TokenGeneratorInterface;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;
use RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\CreateUser
 */
class CreateUserTest extends TestCase
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

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

        $repository = m::mock(UserRepositoryInterface::class);
        $dispatcher = m::mock(Dispatcher::class);
        $validation = m::mock(Factory::class);
        $generator  = m::mock(TokenGeneratorInterface::class);

        $app = new Application();

//        $dispatcher = $this->app->make(Dispatcher::class);

//        $app->bind('validator', function () {
//            return m::mock(Factory::class);
//        });

        $app->bind(TokenGeneratorInterface::class, function () use (&$generator) {
            return $generator;
        });

        // FIXME: Hmm no we should use the real validator?
        $app->bind(Factory::class, function () use (&$validation) {
            return $validation;
        });

        $app->bind(Dispatcher::class, function () use (&$dispatcher) {
            return $dispatcher;
        });

        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
        $this->validation = $validation;
        $this->generator  = $generator;
        $this->app        = $app;
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandle()
    {
        $name  = 'Jill';
        $email = 'jill@example.com';
        $password = 'qwErtY1$R';
        $this->generator->shouldReceive('generateRandom')->with(15)->andReturn($password);
        $this->validation->shouldReceive('make')->andReturnSelf();
        $this->validation->shouldReceive('passes')->andReturn(true);

        $user = m::mock(User::class);
        $user->shouldReceive('getAttribute')->with('email')->andReturn($email);

        $this->repository->shouldReceive('create')->with([
            'name' => $name,
            'email' => $email,
            'password' => $password
        ])->andReturn($user);

        // FIXME: Can't we use the "real" mock dispatcher?
        $this->dispatcher->shouldReceive('dispatch')->with(m::type(UserWasCreated::class));

        $command = new CreateUser($this->repository);
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
        $name  = 'Jill';
        $email = 'jill@example.com';
        $password = 'qwErtY1$R';
        $this->generator->shouldReceive('generateRandom')->with(15)->andReturn($password);
        $this->validation->shouldReceive('make')->andReturnSelf();
        $this->validation->shouldReceive('passes')->andReturn(true);

        $user = m::mock(User::class);
        $user->shouldReceive('getAttribute')->with('email')->andReturn($email);

        $this->repository->shouldReceive('create')->with([
            'name' => $name,
            'email' => $email,
            'password' => $password
        ])->andReturn($user);

        $this->dispatcher->shouldNotReceive('dispatch')->with(m::type(UserWasCreated::class));

        $command = new CreateUser($this->repository);
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
        $name  = 'Jill';
        $email = 'jill@example.com';
        $password = 'qwErtY1$R';

        $this->validation->shouldReceive('make')->andReturnSelf();
        $this->validation->shouldReceive('passes')->andReturn(false);
        $this->validation->shouldReceive('errors')->andReturnSelf();
        $this->validation->shouldReceive('first')->andReturnSelf();

        $this->expectException(RuntimeException::class);

        $command = new CreateUser($this->repository);
        $command->setLaravel($this->app);

        $this->runCommand($command, ['name' => $name, 'email' => $email, 'password' => $password]);
    }

    protected function runCommand($command, $input = [])
    {
        $output = new BufferedOutput();

        $command->run(new ArrayInput($input), $output);

        return $output->fetch();
    }
}
