<?php

namespace App\Repositories;

use App\NotifyEmail;
use App\Repositories\Contracts\NotifyEmailRepositoryInterface;
use App\Repositories\EloquentRepository;

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
