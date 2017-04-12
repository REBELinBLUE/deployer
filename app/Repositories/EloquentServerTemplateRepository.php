<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Repositories\Contracts\ServerTemplateRepositoryInterface;
use REBELinBLUE\Deployer\ServerTemplate;

class EloquentServerTemplateRepository extends EloquentRepository implements ServerTemplateRepositoryInterface
{

    function __construct(ServerTemplate $model)
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
        // TODO: Implement queryByName() method.
    }
}
