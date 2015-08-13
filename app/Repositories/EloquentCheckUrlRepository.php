<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Repositories\Contracts\CheckUrlRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;

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
