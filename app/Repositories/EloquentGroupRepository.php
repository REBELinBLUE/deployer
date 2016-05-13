<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Group;
use REBELinBLUE\Deployer\Contracts\Repositories\GroupRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Template;

/**
 * The group repository.
 */
class EloquentGroupRepository extends EloquentRepository implements GroupRepositoryInterface
{
    /**
     * Class constructor.
     *
     * @param  Group                   $model
     * @return EloquentGroupRepository
     */
    public function __construct(Group $model)
    {
        $this->model = $model;
    }

    /**
     * Gets all groups.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->model
                    ->where('id', '<>', Template::GROUP_ID)
                    ->orderBy('order')
                    ->get();
    }
}
