<?php

namespace REBELinBLUE\Deployer\Repositories\Contracts;

interface ProjectServerRepositoryInterface
{
    /**
     * @param int $model_id
     */
    public function queueForTesting($model_id);
}
