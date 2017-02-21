<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Repositories\Contracts\ChannelRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentChannelRepository;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentChannelRepository
 */
class EloquentChannelRepositoryTest extends EloquentRepositoryTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $this->assertExtendsEloquentRepository(Channel::class, EloquentChannelRepository::class);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsChannelRepositoryInterface()
    {
        $this->assertImplementsRepositoryInterface(
            Channel::class,
            EloquentChannelRepository::class,
            ChannelRepositoryInterface::class
        );
    }
}
