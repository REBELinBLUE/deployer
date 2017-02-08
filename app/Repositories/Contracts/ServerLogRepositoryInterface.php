<?php

namespace REBELinBLUE\Deployer\Repositories\Contracts;

interface ServerLogRepositoryInterface
{
    /**
     * @param array $fields
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $fields);
}
