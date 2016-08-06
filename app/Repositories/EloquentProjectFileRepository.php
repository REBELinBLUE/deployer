<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Contracts\Repositories\ProjectFileRepositoryInterface;
use REBELinBLUE\Deployer\ProjectFile;

/**
 * The project file repository.
 */
class EloquentProjectFileRepository extends EloquentRepository implements ProjectFileRepositoryInterface
{
    /**
     * EloquentProjectFileRepository constructor.
     *
     * @param ProjectFile $model
     */
    public function __construct(ProjectFile $model)
    {
        $this->model = $model;
    }
}
