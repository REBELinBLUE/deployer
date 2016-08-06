<?php

namespace REBELinBLUE\Deployer\Contracts\Repositories;

interface HeartbeatRepositoryInterface
{
    /**
     * @param string $hash
     *
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getByHash($hash);

    /**
     * @param array $fields
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $fields);

    /**
     * @param array $fields
     * @param int $model_id
     *
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function updateById(array $fields, $model_id);

    /**
     * @param int $model_id
     *
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deleteById($model_id);
}
