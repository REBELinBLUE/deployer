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
class EloquentSharedFileRepositoryTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(SharedFile::class);
        $repository = new EloquentSharedFileRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsSharedFileRepositoryInterface()
    {
        $model      = m::mock(SharedFile::class);
        $repository = new EloquentSharedFileRepository($model);

        $this->assertInstanceOf(SharedFileRepositoryInterface::class, $repository);
    }
}
