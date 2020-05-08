<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
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
     * @return array
     */
    public function ping(string $hash): array
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
     * @return JsonResponse
     */
    public function store(StoreHeartbeatRequest $request, ResponseFactory $response): JsonResponse
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
     * @return Model
     */
    public function update(int $heartbeat_id, StoreHeartbeatRequest $request): Model
    {
        return $this->repository->updateById($request->only(
            'name',
            'interval'
        ), $heartbeat_id);
    }
}
