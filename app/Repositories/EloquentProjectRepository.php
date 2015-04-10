<?php namespace App\Repositories;

use App\Project;

use App\Repositories\Contracts\ProjectRepositoryInterface;

/**
 * The project repository
 */
class EloquentProjectRepository extends EloquentRepository implements ProjectRepositoryInterface
{
    /**
     * Class constructor
     * 
     * @param Project $model
     */
    public function __construct(Project $model)
    {
        $this->model = $model;
    }

    /**
     * Gets all projects
     *
     * @return array
     */ 
    public function getAll()
    {
        return $this->model->orderBy('name')->get();
    }
}