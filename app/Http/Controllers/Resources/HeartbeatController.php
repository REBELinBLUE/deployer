<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use Illuminate\Contracts\Routing\ResponseFactory;
use REBELinBLUE\Deployer\Http\Controllers\Controller;
use REBELinBLUE\Deployer\Http\Requests\StoreHeartbeatRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\HeartbeatRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for managing notifications.
 */
class HeartbeatController extends Controller
{
    use ResourceController;

    /**
     * HeartbeatController constructor.
     *
     * @param HeartbeatRepositoryInterface $repository
     */
    public function __construct(HeartbeatRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Handles the callback URL for the heartbeat.
     *
     * @param string $hash
     *
     * @return \Illuminate\View\View
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
     * @param StoreHeartbeatRequest $request
     * @param ResponseFactory       $response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreHeartbeatRequest $request, ResponseFactory $response)
    {
        return $response->json($this->repository->create($request->only(
            'name',
            'interval',
            'project_id'
        )), Response::HTTP_CREATED);
    }

    /**
     * Update the specified heartbeat in storage.
     *
     * @param int                   $heartbeat_id
     * @param StoreHeartbeatRequest $request
     *
     * @return \Illuminate\View\View
     */
    public function update($heartbeat_id, StoreHeartbeatRequest $request)
    {
        return $this->repository->updateById($request->only(
            'name',
            'interval'
        ), $heartbeat_id);
    }
}
