<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Repositories\Contracts\NotificationRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentNotificationRepository;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentNotificationRepository
 */
class EloquentNotificationRepositoryTest extends EloquentRepositoryTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $this->assertExtendsEloquentRepository(Channel::class, EloquentNotificationRepository::class);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsNotificationRepositoryInterface()
    {
        $this->assertImplementsRepositoryInterface(
            Channel::class,
            EloquentNotificationRepository::class,
            NotificationRepositoryInterface::class
        );
    }
}
