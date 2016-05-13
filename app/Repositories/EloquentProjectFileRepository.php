<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\ProjectFile;
use REBELinBLUE\Deployer\Contracts\Repositories\ProjectFileRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;

/**
 * The project file repository.
 */
class EloquentProjectFileRepository extends EloquentRepository implements ProjectFileRepositoryInterface
{
    /**
     * Class constructor.
     *
     * @param  ProjectFile                    $model
     * @return ProjectFileRepositoryInterface
     */
    public function __construct(ProjectFile $model)
    {
        $this->model = $model;
    }
}
