<?php

namespace REBELinBLUE\Deployer\Repositories;

/**
 * Abstract class for eloquent repositories.
 */
abstract class EloquentRepository
{
    /**
     * An instance of the model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Get's all records from the model.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get's an item from the repository.
     *
     * @param int $model_id
     *
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById($model_id)
    {
        return $this->model->findOrFail($model_id);
    }

    /**
     * Creates a new instance of the model.
     *
     * @param array $fields
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $fields)
    {
        return $this->model->create($fields);
    }

    /**
     * Updates an instance by it's ID.
     *
     * @param array $fields
     * @param int $model_id
     *
     * @return \Illuminate\Database\Eloquent\Model
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
     * @param int $model_id
     *
     * @return bool
     * @throws \Exception
     */
    public function deleteById($model_id)
    {
        $model = $this->getById($model_id);

        return $model->delete();
    }
}
