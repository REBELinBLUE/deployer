<?php

namespace App\Repositories\Contracts;

interface ServerRepositoryInterface
{
    public function getById($model_id);
    public function create(array $fields);
    public function updateById(array $fields, $model_id);
    public function deleteById($model_id);
    public function queueForTesting($model_id);
}
