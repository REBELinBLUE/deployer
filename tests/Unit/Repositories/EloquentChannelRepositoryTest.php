<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Repositories\Contracts\ChannelRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentChannelRepository;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentChannelRepository
 */
class EloquentChannelRepositoryTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(Channel::class);
        $repository = new EloquentChannelRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsChannelRepositoryInterface()
    {
        $model      = m::mock(Channel::class);
        $repository = new EloquentChannelRepository($model);

        $this->assertInstanceOf(ChannelRepositoryInterface::class, $repository);
    }
}
