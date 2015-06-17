<?php

namespace App\Repositories;

use App\Group;
use App\Repositories\Contracts\GroupRepositoryInterface;
use App\Repositories\EloquentRepository;

/**
 * The group repository.
 */
class EloquentGroupRepository extends EloquentRepository implements GroupRepositoryInterface
{
    /**
     * Class constructor.
     *
     * @param Group $model
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
        return $this->model->orderBy('name')->get();
    }
}
