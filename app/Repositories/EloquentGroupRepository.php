<?php namespace App\Repositories;

use App\Group;
use App\Repositories\Contracts\GroupRepositoryInterface;

/**
 * The group repository
 */
class EloquentGroupRepository implements GroupRepositoryInterface
{
    /**
     * Gets all groups
     *
     * @return array
     */ 
    public function getAll()
    {
        $groups = Group::orderBy('name')
                       ->get();

        foreach ($groups as $group) {
            $group->project_count = count($group->projects);
        }

        return $groups;
    }
}