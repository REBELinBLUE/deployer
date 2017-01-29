<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Repositories\Contracts\CheckUrlRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentCheckUrlRepository;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentCheckUrlRepository
 */
class EloquentCheckUrlRepositoryTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(CheckUrl::class);
        $repository = new EloquentCheckUrlRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsCheckUrlRepositoryInterface()
    {
        $model      = m::mock(CheckUrl::class);
        $repository = new EloquentCheckUrlRepository($model);

        $this->assertInstanceOf(CheckUrlRepositoryInterface::class, $repository);
    }
}
