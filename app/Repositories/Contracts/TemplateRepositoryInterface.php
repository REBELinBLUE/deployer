<?php

namespace App\Repositories\Contracts;

interface TemplateRepositoryInterface
{
    public function getAll();
    public function getById($model_id);
}
