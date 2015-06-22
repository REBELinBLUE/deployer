<?php

namespace App\Repositories\Contracts;

interface CommandRepositoryInterface
{
    public function create(array $fields);
    public function updateById(array $fields, $model_id);
    public function deleteById($model_id);
    public function getForDeployStep($project_id, $step);
}
