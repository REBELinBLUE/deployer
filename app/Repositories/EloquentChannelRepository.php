<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Repositories\Contracts\ChannelRepositoryInterface;

/**
 * The notification channel repository.
 */
class EloquentChannelRepository extends EloquentRepository implements ChannelRepositoryInterface
{
    /**
     * EloquentChannelRepository constructor.
     *
     * @param Channel $model
     */
    public function __construct(Channel $model)
    {
        $this->model = $model;
    }
}
