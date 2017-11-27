<?php

namespace REBELinBLUE\Deployer\Repositories\Contracts;

interface RevisionRepositoryInterface
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTypes();

    /**
     * @param string $type
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getInstances($type);

    /**
     * @param int $paginate
     * @param null $filterByType
     * @param null $filterByInstance
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLogEntries($paginate = 50, $filterByType = null, $filterByInstance = null);
}
