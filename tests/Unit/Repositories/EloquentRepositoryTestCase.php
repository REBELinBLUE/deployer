<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Tests\TestCase;

abstract class EloquentRepositoryTestCase extends TestCase
{
    protected function assertExtendsEloquentRepository($model, $class)
    {
        $mock       = m::mock($model);
        $repository = new $class($mock);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    protected function assertImplementsRepositoryInterface($model, $class, $interface)
    {
        $mock       = m::mock($model);
        $repository = new $class($mock);

        $this->assertInstanceOf($interface, $repository);
    }
}
