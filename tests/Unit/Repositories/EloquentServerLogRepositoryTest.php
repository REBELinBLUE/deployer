<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use REBELinBLUE\Deployer\Repositories\Contracts\ServerLogRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentServerLogRepository;
use REBELinBLUE\Deployer\ServerLog;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentServerLogRepository
 */
class EloquentServerLogRepositoryTest extends EloquentRepositoryTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $this->assertExtendsEloquentRepository(ServerLog::class, EloquentServerLogRepository::class);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsConfigFileRepositoryInterface()
    {
        $this->assertImplementsRepositoryInterface(
            ServerLog::class,
            EloquentServerLogRepository::class,
            ServerLogRepositoryInterface::class
        );
    }
}
