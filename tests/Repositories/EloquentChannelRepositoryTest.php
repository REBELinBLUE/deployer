<?php

namespace REBELinBLUE\Deployer\Tests\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Repositories\Contracts\ChannelRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentChannelRepository;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Tests\TestCase;

class EloquentChannelRepositoryTest extends TestCase
{
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(Channel::class);
        $repository = new EloquentChannelRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    public function testImplementsChannelRepositoryInterfaceInterface()
    {
        $model      = m::mock(Channel::class);
        $repository = new EloquentChannelRepository($model);

        $this->assertInstanceOf(ChannelRepositoryInterface::class, $repository);
    }
}
