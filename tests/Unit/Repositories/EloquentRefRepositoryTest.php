<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\Ref;
use REBELinBLUE\Deployer\Repositories\Contracts\RefRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentRefRepository;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentRefRepository
 */
class EloquentRefRepositoryTest extends EloquentRepositoryTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $this->assertExtendsEloquentRepository(Ref::class, EloquentRefRepository::class);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsRefRepositoryInterface()
    {
        $this->assertImplementsRepositoryInterface(
            Ref::class,
            EloquentRefRepository::class,
            RefRepositoryInterface::class
        );
    }
}
