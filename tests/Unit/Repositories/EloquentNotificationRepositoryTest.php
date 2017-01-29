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
class EloquentNotificationRepositoryTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(Notification::class);
        $repository = new EloquentNotificationRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsNotificationRepositoryInterface()
    {
        $model      = m::mock(Notification::class);
        $repository = new EloquentNotificationRepository($model);

        $this->assertInstanceOf(NotificationRepositoryInterface::class, $repository);
    }
}
