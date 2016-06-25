<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Contracts\Repositories\NotifyEmailRepositoryInterface;
use REBELinBLUE\Deployer\NotifyEmail;

/**
 * The notification email repository.
 */
class EloquentNotifyEmailRepository extends EloquentRepository implements NotifyEmailRepositoryInterface
{
    /**
     * EloquentNotifyEmailRepository constructor.
     *
     * @param NotifyEmail $model
     */
    public function __construct(NotifyEmail $model)
    {
        $this->model = $model;
    }
}
