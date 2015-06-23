<?php

namespace App\Repositories;

use App\CheckUrl;
use App\Repositories\Contracts\CheckUrlRepositoryInterface;
use App\Repositories\EloquentRepository;

/**
 * The notification email repository.
 */
class EloquentCheckUrlRepository extends EloquentRepository implements CheckUrlRepositoryInterface
{
    /**
     * Class constructor.
     *
     * @param  CheckUrl                   $model
     * @return EloquentCheckUrlRepository
     */
    public function __construct(CheckUrl $model)
    {
        $this->model = $model;
    }

    /**
     * Creates a new instance of the model.
     *
     * @param  array $fields
     * @return Model
     */
    public function create(array $fields)
    {
        $url = $this->model->create($fields);

        //$this->dispatch(new RequestProjectCheckUrl([$url])); // FIXME: Should this be a model event?

        return $url;
    }

    /**
     * Updates an instance by it's ID.
     *
     * @param  array $fields
     * @param  int   $model_id
     * @return Model
     */
    public function updateById(array $fields, $model_id)
    {
        $url = $this->getById($model_id);

        $old_url = $url->url;

        $url->update($fields);

        if ($old_url !== $url->url) {
            //$this->dispatch(new RequestProjectCheckUrl([$url]));
        }

        return $url;
    }
}
