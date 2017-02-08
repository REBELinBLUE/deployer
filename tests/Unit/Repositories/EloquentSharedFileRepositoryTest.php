<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\Repositories\Contracts\SharedFileRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Repositories\EloquentSharedFileRepository;
use REBELinBLUE\Deployer\SharedFile;
use REBELinBLUE\Deployer\Tests\TestCase;

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
