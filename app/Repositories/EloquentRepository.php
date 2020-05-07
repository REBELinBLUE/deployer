<?php

namespace REBELinBLUE\Deployer\Repositories;

/**
 * Abstract class for eloquent repositories.
 * @SuppressWarnings(PHPMD.NumberOfChildren)
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
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getById(int $model_id)
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
     * @param int   $model_id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateById(array $fields, int $model_id)
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
     * @throws \Exception
     * @return bool
     */
    public function deleteById(int $model_id)
    {
        $model = $this->getById($model_id);

        return $model->delete();
    }

    /**
     * Chunk the results of the query.
     *
     * @param int      $count
     * @param callable $callback
     *
     * @return bool
     */
    public function chunk(int $count, callable $callback)
    {
        return $this->model->chunk($count, $callback);
    }

    /**
     * Runs where query in chunk.
     *
     * @param string   $field
     * @param array    $values
     * @param int      $count
     * @param callable $callback
     *
     * @return bool
     */
    public function chunkWhereIn(string $field, array $values, int $count, callable $callback)
    {
        return $this->model->whereIn($field, $values, 'and', false)
                           ->chunk($count, $callback);
    }

    /**
     * Updates all instances with a specific status.
     *
     * @param int $original
     * @param int $updated
     *
     * @return bool
     */
    public function updateStatusAll(int $original, int $updated)
    {
        return $this->model->where('status', '=', $original)
                           ->update(['status' => $updated]);
    }
}
