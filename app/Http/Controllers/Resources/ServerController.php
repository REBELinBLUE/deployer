<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use REBELinBLUE\Deployer\Http\Controllers\Controller;
use REBELinBLUE\Deployer\Http\Requests\StoreServerRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectServerRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ServerRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Server management controller.
 */
class ServerController extends Controller
{
    use ResourceController;

    /**
     * @var ProjectServerRepositoryInterface
     */
    private $projectServerRepository;

    /**
     * ServerController constructor.
     *
     * @param ServerRepositoryInterface $repository
     */
    public function __construct(
        ProjectServerRepositoryInterface $projectServerRepository,
        ServerRepositoryInterface $repository
    ) {
        $this->repository = $repository;
        $this->projectServerRepository = $projectServerRepository;
    }

    /**
     * Store a newly created server in storage.
     *
     * @param StoreServerRequest $request
     * @param ResponseFactory    $response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreServerRequest $request, ResponseFactory $response)
    {
        return $response->json($this->repository->create($request->only(
            'name',
            'user',
            'ip_address',
            'port',
            'path',
            'project_id',
            'deploy_code',
            'add_commands',
            'server_id'
        )), Response::HTTP_CREATED);
    }

    /**
     * Update the specified server in storage.
     *
     * @param int                $server_id
     * @param StoreServerRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($server_id, StoreServerRequest $request)
    {
        return $this->repository->updateById($request->only(
            'name',
            'user',
            'ip_address',
            'port',
            'path',
            'deploy_code'
        ), $server_id);
    }

    /**
     * Queues a connection test for the specified server.
     *
     * @param int $server_id
     *
     * @return array
     */
    public function test($server_id)
    {
        $this->projectServerRepository->queueForTesting($server_id);

        return [
            'success' => true,
        ];
    }

    /**
     * Re-generates the order for the supplied servers.
     *
     * @param Request $request
     *
     * @return array
     */
    public function reorder(Request $request)
    {
        $order = 0;

        foreach ($request->get('servers') as $server_id) {
            $this->repository->updateById([
                'order' => $order,
            ], $server_id);

            $order++;
        }

        return [
            'success' => true,
        ];
    }
}
