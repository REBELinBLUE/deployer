<?php

namespace App\Repositories;

use App\Project;
use App\Repositories\Contracts\TemplateRepositoryInterface;

/**
 * The template repository.
 */
class EloquentTemplateRepository extends EloquentRepository implements TemplateRepositoryInterface
{
    /**
     * Class constructor.
     *
     * @param Project $model
     * @return EloquentProjectRepository
     */
    public function __construct(Project $model)
    {
        $this->model = $model;
    }

    /**
     * Gets all templates.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->model
                    ->templates()
                    ->orderBy('name')
                    ->get();
    }
}
