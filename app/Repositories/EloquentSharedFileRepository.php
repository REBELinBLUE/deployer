<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Repositories\Contracts\SharedFileRepositoryInterface;
use REBELinBLUE\Deployer\SharedFile;

/**
 * The shared file repository.
 */
class EloquentSharedFileRepository extends EloquentRepository implements SharedFileRepositoryInterface
{
    /**
     * EloquentSharedFileRepository constructor.
     *
     * @param SharedFile $model
     */
    public function __construct(SharedFile $model)
    {
        $this->model = $model;
    }
}
