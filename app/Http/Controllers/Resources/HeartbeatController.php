<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use REBELinBLUE\Deployer\Contracts\Repositories\HeartbeatRepositoryInterface;
use REBELinBLUE\Deployer\Http\Requests\StoreHeartbeatRequest;

/**
 * Controller for managing notifications.
 */
class HeartbeatController extends ResourceController
{
    /**
     * Class constructor.
     *
     * @param  NotificationRepositoryInterface $repository
     * @return void
     */
    public function __construct(HeartbeatRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Handles the callback URL for the heartbeat.
     *
     * @param  string   $hash The webhook hash
     * @return Response
     */
    public function ping($hash)
    {
        $heartbeat = $this->repository->getByHash($hash);

        $heartbeat->pinged();

        return [
            'success' => true,
        ];
    }

    /**
     * Store a newly created heartbeat in storage.
     *
     * @param  StoreHeartbeatRequest $request
     * @return Response
     */
    public function store(StoreHeartbeatRequest $request)
    {
        return $this->repository->create($request->only(
            'name',
            'interval',
            'project_id'
        ));
    }

    /**
     * Update the specified heartbeat in storage.
     *
     * @param  int                   $heartbeat_id
     * @param  StoreHeartbeatRequest $request
     * @return Response
     */
    public function update($heartbeat_id, StoreHeartbeatRequest $request)
    {
        return $this->repository->updateById($request->only(
            'name',
            'interval'
        ), $heartbeat_id);
    }
}
