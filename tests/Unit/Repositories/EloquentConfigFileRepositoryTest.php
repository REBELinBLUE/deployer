<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\ConfigFile;
use REBELinBLUE\Deployer\Repositories\Contracts\ConfigFileRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentConfigFileRepository;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentConfigFileRepository
 */
class EloquentConfigFileRepositoryTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(ConfigFile::class);
        $repository = new EloquentConfigFileRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsConfigFileRepositoryInterface()
    {
        $model      = m::mock(ConfigFile::class);
        $repository = new EloquentConfigFileRepository($model);

        $this->assertInstanceOf(ConfigFileRepositoryInterface::class, $repository);
    }
}
