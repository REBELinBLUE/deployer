<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Repositories\Contracts\NotificationRepositoryInterface;

/**
 * The notification repository.
 */
class EloquentNotificationRepository extends EloquentRepository implements NotificationRepositoryInterface
{
    /**
     * EloquentNotificationRepository constructor.
     *
     * @param Channel $model
     */
    public function __construct(Channel $model)
    {
        $this->model = $model;
    }
}
