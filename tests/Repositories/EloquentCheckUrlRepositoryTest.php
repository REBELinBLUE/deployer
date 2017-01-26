<?php

namespace REBELinBLUE\Deployer\Tests\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Repositories\Contracts\CheckUrlRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentCheckUrlRepository;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Tests\TestCase;

class EloquentCheckUrlRepositoryTest extends TestCase
{
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(CheckUrl::class);
        $repository = new EloquentCheckUrlRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    public function testImplementsCheckUrlRepositoryInterfaceInterface()
    {
        $model      = m::mock(CheckUrl::class);
        $repository = new EloquentCheckUrlRepository($model);

        $this->assertInstanceOf(CheckUrlRepositoryInterface::class, $repository);
    }
}
