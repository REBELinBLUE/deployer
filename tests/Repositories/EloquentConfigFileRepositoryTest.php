<?php

namespace REBELinBLUE\Deployer\Tests\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\ConfigFile;
use REBELinBLUE\Deployer\Repositories\Contracts\ConfigFileRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentConfigFileRepository;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Tests\TestCase;

class EloquentConfigFileRepositoryTest extends TestCase
{
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(ConfigFile::class);
        $repository = new EloquentConfigFileRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    public function testImplementsConfigFileRepositoryInterfaceInterface()
    {
        $model      = m::mock(ConfigFile::class);
        $repository = new EloquentConfigFileRepository($model);

        $this->assertInstanceOf(ConfigFileRepositoryInterface::class, $repository);
    }
}
