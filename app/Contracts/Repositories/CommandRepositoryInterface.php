<?php

namespace REBELinBLUE\Deployer\Contracts\Repositories;

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

    /**
     * @param int $project_id
     * @param int $step
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getForDeployStep($project_id, $step);
}
