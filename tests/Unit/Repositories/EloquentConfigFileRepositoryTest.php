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
class EloquentConfigFileRepositoryTest extends EloquentRepositoryTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $this->assertExtendsEloquentRepository(ConfigFile::class, EloquentConfigFileRepository::class);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsConfigFileRepositoryInterface()
    {
        $this->assertImplementsRepositoryInterface(
            ConfigFile::class,
            EloquentConfigFileRepository::class,
            ConfigFileRepositoryInterface::class
        );
    }
}
