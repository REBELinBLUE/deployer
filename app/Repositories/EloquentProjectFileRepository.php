<?php

namespace App\Repositories;

use App\ProjectFile;
use App\Repositories\Contracts\ProjectFileRepositoryInterface;
use App\Repositories\EloquentRepository;

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
