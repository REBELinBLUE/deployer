<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Foundation\Application;
use Mockery as m;
use REBELinBLUE\Deployer\Console\Commands\CreateUser;
use REBELinBLUE\Deployer\Repositories\Contracts\UserRepositoryInterface;
use REBELinBLUE\Deployer\Services\Token\TokenGeneratorInterface;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
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

//        $app->bind('validator', function () {
//            return m::mock(Factory::class);
//        });

        $app->bind(TokenGeneratorInterface::class, function () use (&$generator) {
            return $generator;
        });

        $app->bind(Factory::class, function () use (&$validation) {
            return $validation;
        });

        $app->bind(Dispatcher::class, function () use (&$dispatcher) {
            return $dispatcher;
        });

        $app->bind(UserRepositoryInterface::class, function () use (&$repository) {
            return $repository;
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
        $this->markTestIncomplete('not working');

        $name  = 'Jill';
        $email = 'jill@example.com';

        $command = new CreateUser($repository = m::mock(UserRepositoryInterface::class));
        $command->setLaravel($this->app);

        $this->runCommand($command, ['name' => $name, 'email' => $email]);
    }

    protected function runCommand($command, $input = [])
    {
        return $command->run(new ArrayInput($input), new NullOutput());
    }
}
