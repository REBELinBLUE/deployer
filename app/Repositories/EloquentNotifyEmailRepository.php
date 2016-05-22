<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Contracts\Repositories\NotifyEmailRepositoryInterface;
use REBELinBLUE\Deployer\NotifyEmail;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;

/**
 * The notification email repository.
 */
class EloquentNotifyEmailRepository extends EloquentRepository implements NotifyEmailRepositoryInterface
{
    /**
     * Class constructor.
     *
     * @param  NotifyEmail                   $model
     * @return EloquentNotifyEmailRepository
     */
    public function __construct(NotifyEmail $model)
    {
        $this->model = $model;
    }
}
