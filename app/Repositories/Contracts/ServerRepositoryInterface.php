<?php

namespace REBELinBLUE\Deployer\Repositories\Contracts;

interface ServerRepositoryInterface
{
    /**
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getById(int $model_id);

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
     * @param int $model_id
     */
    public function queueForTesting(int $model_id): void;

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function queryByName(string $name);
}
