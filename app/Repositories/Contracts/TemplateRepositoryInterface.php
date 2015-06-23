<?php

namespace App\Repositories\Contracts;

interface TemplateRepositoryInterface
{
    public function getAll();
    public function getById($model_id);
    public function create(array $fields);
    public function updateById(array $fields, $model_id);
}
