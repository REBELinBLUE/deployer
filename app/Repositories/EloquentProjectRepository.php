<?php namespace App\Repositories;

use App\Project;

use App\Repositories\Contracts\ProjectRepositoryInterface;

/**
 * The project repository
 */
class EloquentProjectRepository implements ProjectRepositoryInterface
{
    /**
     * Gets all projects
     *
     * @return array
     */ 
    public function getAll()
    {
        return Project::orderBy('name')
                      ->get();
    }
}