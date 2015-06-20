<?php

namespace App\Repositories;

/**
 * Abstract class for eloquent repositories.
 */
abstract class EloquentRepository
{
    /**
     * An instance of the model.
     *
     * @var Model
     */
    protected $model;

    /**
     * Get's all records from the model.
     *
     * @return Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get's an item from the repository.
     *
     * @param  int   $model_id
     * @return model
     */
    public function getById($model_id)
    {
        return $this->model->findOrFail($model_id);
    }

    /**
     * Creates a new instance of the model.
     *
     * @param  array $fields
     * @return Model
     */
    public function create(array $fields)
    {
        return $this->model->create($fields);
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
        $model = $this->getById($model_id);

        $model->update($fields);

        return $model;
    }

    /**
     * Delete an instance by it's ID.
     *
     * @param  int   $model_id
     * @return Model
     */
    public function deleteById($model_id)
    {
        $model = $this->getById($model_id);

        $model->delete();

        return;
    }
}
