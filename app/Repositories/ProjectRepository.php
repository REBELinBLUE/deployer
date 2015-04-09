<?php namespace App\Repositories;

use App\Project;

/**
 * The project repository
 */
class ProjectRepository
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