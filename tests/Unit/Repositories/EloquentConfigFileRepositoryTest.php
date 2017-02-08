<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use REBELinBLUE\Deployer\ConfigFile;
use REBELinBLUE\Deployer\Repositories\Contracts\ConfigFileRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentConfigFileRepository;

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
