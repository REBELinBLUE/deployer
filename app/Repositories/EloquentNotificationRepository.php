<?php

namespace App\Repositories;

use App\Notification;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Repositories\EloquentRepository;

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
