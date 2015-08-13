<?php

namespace REBELinBLUE\Deployer\Repositories\Contracts;

interface NotificationRepositoryInterface
{
    public function create(array $fields);
    public function updateById(array $fields, $model_id);
    public function deleteById($model_id);
}
