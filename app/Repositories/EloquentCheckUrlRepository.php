<?php

namespace App\Repositories;

use App\CheckUrl;
use App\Repositories\Contracts\CheckUrlRepositoryInterface;
use App\Repositories\EloquentRepository;

/**
 * The notification email repository.
 */
class EloquentCheckUrlRepository extends EloquentRepository implements CheckUrlRepositoryInterface
{
    /**
     * Class constructor.
     *
     * @param  CheckUrl                   $model
     * @return EloquentCheckUrlRepository
     */
    public function __construct(CheckUrl $model)
    {
        $this->model = $model;
    }
}
