<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Contracts\Repositories\ConfigFileRepositoryInterface;
use REBELinBLUE\Deployer\ConfigFile;

/**
 * The project file repository.
 */
class EloquentConfigFileRepository extends EloquentRepository implements ConfigFileRepositoryInterface
{
    /**
     * EloquentConfigFileRepository constructor.
     *
     * @param ConfigFile $model
     */
    public function __construct(ConfigFile $model)
    {
        $this->model = $model;
    }
}
