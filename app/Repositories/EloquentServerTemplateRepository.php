<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Repositories\Contracts\ServerTemplateRepositoryInterface;
use REBELinBLUE\Deployer\ServerTemplate;

/**
 * Class EloquentServerTemplateRepository.
 */
class EloquentServerTemplateRepository extends EloquentRepository implements ServerTemplateRepositoryInterface
{
    /**
     * EloquentServerTemplateRepository constructor.
     *
     * @param ServerTemplate $model
     */
    public function __construct(ServerTemplate $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function queryByName($name)
    {
        /* @var ServerTemplate $model */
        return $this->model->where('name', '=', $name);
    }
}
