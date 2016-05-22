<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Contracts\Repositories\NotificationRepositoryInterface;
use REBELinBLUE\Deployer\Notification;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;

/**
 * The notification repository.
 */
class EloquentNotificationRepository extends EloquentRepository implements NotificationRepositoryInterface
{
    /**
     * Class constructor.
     *
     * @param  Notification                   $model
     * @return EloquentNotificationRepository
     */
    public function __construct(Notification $model)
    {
        $this->model = $model;
    }
}
