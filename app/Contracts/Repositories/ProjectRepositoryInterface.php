<?php

namespace REBELinBLUE\Deployer\Contracts\Repositories;

interface ProjectRepositoryInterface
{
    /**
     * @param string $hash
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getByHash($hash);

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll();

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
    public function updateById(array $fields, $model_id);

    /**
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return bool
     */
    public function deleteById($model_id);
}
