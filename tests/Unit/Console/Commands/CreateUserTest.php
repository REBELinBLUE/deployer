<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Validation\Factory;
use Mockery as m;
use REBELinBLUE\Deployer\Repositories\Contracts\UserRepositoryInterface;
use REBELinBLUE\Deployer\Services\Token\TokenGeneratorInterface;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Console\Commands\CreateUser;

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

        $generator = m::mock(TokenGeneratorInterface::class);

        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
        $this->validation = $validation;
        $this->generator = $generator;
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandle()
    {
        $this->markTestIncomplete('not yet done');
        // see https://github.com/laravel/framework/blob/5.4/tests/Database/DatabaseMigrationMakeCommandTest.php
        $name = 'Jill';
        $email = 'jill@example.com';


        $console = new CreateUser($this->repository);
//        $console->addArgument('name', $name);
//        $console->addArgument('email', $email);



        $console->handle($this->dispatcher, $this->validation, $this->generator);
    }
}
