<?php

namespace REBELinBLUE\Deployer\Tests\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\Repositories\Contracts\SharedFileRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Repositories\EloquentSharedFileRepository;
use REBELinBLUE\Deployer\SharedFile;
use REBELinBLUE\Deployer\Tests\TestCase;

class EloquentSharedFileRepositoryTest extends TestCase
{
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(SharedFile::class);
        $repository = new EloquentSharedFileRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    public function testImplementsSharedFileRepositoryInterfaceInterface()
    {
        $model      = m::mock(SharedFile::class);
        $repository = new EloquentSharedFileRepository($model);

        $this->assertInstanceOf(SharedFileRepositoryInterface::class, $repository);
    }
}
