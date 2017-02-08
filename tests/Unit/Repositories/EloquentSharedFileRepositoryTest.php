<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use REBELinBLUE\Deployer\Repositories\Contracts\SharedFileRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentSharedFileRepository;
use REBELinBLUE\Deployer\SharedFile;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentSharedFileRepository
 */
class EloquentSharedFileRepositoryTest extends EloquentRepositoryTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $this->assertExtendsEloquentRepository(SharedFile::class, EloquentSharedFileRepository::class);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsSharedFileRepositoryInterface()
    {
        $this->assertImplementsRepositoryInterface(
            SharedFile::class,
            EloquentSharedFileRepository::class,
            SharedFileRepositoryInterface::class
        );
    }
}
