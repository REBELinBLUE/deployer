<?php

namespace REBELinBLUE\Deployer\Repositories\Contracts;

interface CommandRepositoryInterface
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
    public function updateById(array $fields, $model_id);

    /**
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return bool
     */
    public function deleteById($model_id);

    /**
     * @param int    $target_id
     * @param string $target
     * @param int    $step
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getForDeployStep($target_id, $target, $step);
}
