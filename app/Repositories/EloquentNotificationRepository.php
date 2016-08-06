<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Contracts\Repositories\NotificationRepositoryInterface;
use REBELinBLUE\Deployer\Notification;

/**
 * The notification repository.
 */
class EloquentNotificationRepository extends EloquentRepository implements NotificationRepositoryInterface
{
    /**
     * EloquentNotificationRepository constructor.
     *
     * @param Notification $model
     */
    public function __construct(Notification $model)
    {
        $this->model = $model;
    }
}
