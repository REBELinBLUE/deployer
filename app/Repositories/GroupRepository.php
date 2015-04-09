<?php namespace App\Repositories;

use App\Group;

/**
 * The group repository
 */
class GroupRepository
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