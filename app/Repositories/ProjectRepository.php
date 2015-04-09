<?php namespace App\Repositories;

use App\Project;

class ProjectRepository
{
    public function getAll()
    {
        return Project::orderBy('name')
                      ->get();
    }
}