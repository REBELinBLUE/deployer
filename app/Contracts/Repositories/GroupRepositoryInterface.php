<?php

namespace REBELinBLUE\Deployer\Contracts\Repositories;

interface GroupRepositoryInterface
{
    public function getAll();
    public function create(array $fields);
    public function updateById(array $fields, $model_id);
}
