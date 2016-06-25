<?php

namespace REBELinBLUE\Deployer\Contracts\Repositories;

interface ServerRepositoryInterface
{
    /**
     * @param int $model_id
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById($model_id);

    /**
     * @param array $fields
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $fields);

    /**
     * @param array $fields
     * @param int $model_id
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function updateById(array $fields, $model_id);

    /**
     * @param int $model_id
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deleteById($model_id);

    /**
     * @param int $model_id
     * @return mixed
     */
    public function queueForTesting($model_id);
}
