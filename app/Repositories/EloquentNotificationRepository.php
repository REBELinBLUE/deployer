<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Notification;
use REBELinBLUE\Deployer\Repositories\Contracts\NotificationRepositoryInterface;

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
