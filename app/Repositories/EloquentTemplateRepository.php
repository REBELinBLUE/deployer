<?php namespace App\Repositories;

use App\Template;
use App\Repositories\EloquentRepository;
use App\Repositories\Contracts\TemplateRepositoryInterface;

/**
 * The template repository
 */
class EloquentTemplateRepository extends EloquentRepository implements TemplateRepositoryInterface
{
    /**
     * Class constructor
     *
     * @param Template $model
     * @return EloquentTemplateRepository
     */
    public function __construct(Template $model)
    {
        $this->model = $model;
    }

    /**
     * Gets all templates
     *
     * @return array
     */
    public function getAll()
    {
        return $this->model->orderBy('name')->get();
    }
}
