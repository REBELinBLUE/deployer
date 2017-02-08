<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\Notification;
use REBELinBLUE\Deployer\Repositories\Contracts\NotificationRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentNotificationRepository;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Tests\TestCase;

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
        $this->assertExtendsEloquentRepository(Notification::class, EloquentNotificationRepository::class);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsNotificationRepositoryInterface()
    {
        $this->assertImplementsRepositoryInterface(
            Notification::class,
            EloquentNotificationRepository::class,
            NotificationRepositoryInterface::class
        );
    }
}
