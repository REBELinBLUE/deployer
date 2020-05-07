<?php

namespace REBELinBLUE\Deployer\Repositories\Contracts;

interface CheckUrlRepositoryInterface
{
    /**
     * @param array $fields
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $fields);

    /**
     * @param array $fields
     * @param int   $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateById(array $fields, int $model_id);

    /**
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return bool
     */
    public function deleteById(int $model_id);

    /**
     * @param string   $field
     * @param array    $values
     * @param int      $count
     * @param callable $callback
     *
     * @return bool
     */
    public function chunkWhereIn(string $field, array $values, int $count, callable $callback);
}
