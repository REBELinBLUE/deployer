<?php

namespace REBELinBLUE\Deployer\Contracts\Repositories;

interface VariableRepositoryInterface
{
    public function getAll();
    public function create(array $fields);
    public function updateById(array $fields, $model_id);
    public function deleteById($model_id);
}
