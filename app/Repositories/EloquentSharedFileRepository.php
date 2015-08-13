<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Repositories\Contracts\SharedFileRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\SharedFile;

/**
 * The shared file repository.
 */
class EloquentSharedFileRepository extends EloquentRepository implements SharedFileRepositoryInterface
{
    /**
     * Class constructor.
     *
     * @param  SharedFile                    $model
     * @return SharedFileRepositoryInterface
     */
    public function __construct(SharedFile $model)
    {
        $this->model = $model;
    }
}
