<?php

namespace App\Repositories;

use App\Repositories\Contracts\TemplateRepositoryInterface;
use App\Template;

/**
 * The template repository.
 */
class EloquentTemplateRepository extends EloquentRepository implements TemplateRepositoryInterface
{
    /**
     * Class constructor.
     *
     * @param  Template                  $model
     * @return EloquentProjectRepository
     */
    public function __construct(Template $model)
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

    /**
     * Overwrite the parent method to add the requires fields.
     *
     * @param  array    $fields
     * @return Template
     */
    public function create(array $fields)
    {
        $fields['group_id']    = Template::GROUP_ID;
        $fields['is_template'] = true;

        return parent::create($fields);
    }
}
