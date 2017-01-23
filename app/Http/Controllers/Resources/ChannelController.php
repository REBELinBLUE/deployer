<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use REBELinBLUE\Deployer\Contracts\Repositories\ChannelRepositoryInterface;
use REBELinBLUE\Deployer\Http\Requests\StoreChannelRequest;

/**
 * Controller for managing notifications.
 */
class ChannelController extends ResourceController
{
    /**
     * NotificationController constructor.
     *
     * @param ChannelRepositoryInterface $repository
     */
    public function __construct(ChannelRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Store a newly created notification in storage.
     *
     * @param StoreChannelRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(StoreChannelRequest $request)
    {
        $input = $request->only(
            'name',
            'project_id',
            'type',
            'on_deployment_success',
            'on_deployment_failure',
            'on_link_down',
            'on_link_still_down',
            'on_link_recovered',
            'on_heartbeat_missing',
            'on_heartbeat_still_missing',
            'on_heartbeat_recovered'
        );

        $input['config'] = $request->configOnly();

        return $this->repository->create($input);
    }

    /**
     * Update the specified notification in storage.
     *
     * @param $channel_id
     * @param StoreChannelRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($channel_id, StoreChannelRequest $request)
    {
        $input = $request->only(
            'name',
            'on_deployment_success',
            'on_deployment_failure',
            'on_link_down',
            'on_link_still_down',
            'on_link_recovered',
            'on_heartbeat_missing',
            'on_heartbeat_still_missing',
            'on_heartbeat_recovered'
        );

        $input['config'] = $request->configOnly();

        return $this->repository->updateById($input, $channel_id);
    }
}
