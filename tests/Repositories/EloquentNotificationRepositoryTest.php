<?php

namespace REBELinBLUE\Deployer\Tests\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\Notification;
use REBELinBLUE\Deployer\Repositories\Contracts\NotificationRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentNotificationRepository;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Tests\TestCase;

class EloquentNotificationRepositoryTest extends TestCase
{
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(Notification::class);
        $repository = new EloquentNotificationRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    public function testImplementsNotificationRepositoryInterfaceInterface()
    {
        $model      = m::mock(Notification::class);
        $repository = new EloquentNotificationRepository($model);

        $this->assertInstanceOf(NotificationRepositoryInterface::class, $repository);
    }
}
