<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Contracts\Repositories\GroupRepositoryInterface;
use REBELinBLUE\Deployer\Group;
use REBELinBLUE\Deployer\Template;

/**
 * The group repository.
 */
class EloquentGroupRepository extends EloquentRepository implements GroupRepositoryInterface
{
    /**
     * EloquentGroupRepository constructor.
     *
     * @param Group $model
     */
    public function __construct(Group $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return $this->model
                    ->where('id', '<>', Template::GROUP_ID)
                    ->orderBy('order')
                    ->get();
    }
}
