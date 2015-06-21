<?php

namespace App\Http\Controllers\Resources;

use App\Http\Requests\StoreHeartbeatRequest;
use App\Repositories\Contracts\HeartbeatRepositoryInterface;

/**
 * Controller for managing notifications.
 */
class HeartbeatController extends ResourceController
{
    /**
     * The heartbeat repository.
     *
     * @var NotificationRepositoryInterface
     */
    private $heartbeatRepository;

    /**
     * Class constructor.
     *
     * @param  NotificationRepositoryInterface $notificationRepository
     * @return void
     */
    public function __construct(HeartbeatRepositoryInterface $heartbeatRepository)
    {
        $this->heartbeatRepository = $heartbeatRepository;
    }

    /**
     * Handles the callback URL for the heartbeat.
     *
     * @param  string   $hash The webhook hash
     * @return Response
     */
    public function ping($hash)
    {
        $heartbeat = $this->heartbeatRepository->getByHash($hash);

        $heartbeat->pinged();

        return 'OK';
    }

    /**
     * Store a newly created heartbeat in storage.
     *
     * @param  StoreHeartbeatRequest $request
     * @return Response
     */
    public function store(StoreHeartbeatRequest $request)
    {
        return $this->heartbeatRepository->create($request->only(
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
        return $this->heartbeatRepository->updateById($request->only(
            'name',
            'interval'
        ), $heartbeat_id);
    }

    /**
     * Remove the specified heartbeat from storage.
     *
     * @param  int      $heartbeat_id
     * @return Response
     */
    public function destroy($heartbeat_id)
    {
        $this->heartbeatRepository->deleteById($heartbeat_id);

        return [
            'success' => true,
        ];
    }
}
