<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Repositories\Contracts\TemplateRepositoryInterface;
use REBELinBLUE\Deployer\Template;

/**
 * The template repository.
 */
class EloquentTemplateRepository extends EloquentRepository implements TemplateRepositoryInterface
{
    /**
     * EloquentTemplateRepository constructor.
     *
     * @param Template $model
     */
    public function __construct(Template $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return $this->model
                    ->orderBy('name')
                    ->get();
    }
}
