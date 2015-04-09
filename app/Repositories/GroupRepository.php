<?php namespace App\Repositories;

use App\Group;

class GroupRepository
{
    public function getAll()
    {
        $groups = Group::all();

        foreach ($groups as $group) {
            $group->project_count = count($group->projects);
        }

        return $groups;
    }
}