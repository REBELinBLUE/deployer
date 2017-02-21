<?php

namespace REBELinBLUE\Deployer\Repositories\Contracts;

interface RefRepositoryInterface
{
    /**
     * @param array $fields
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $fields);
}
