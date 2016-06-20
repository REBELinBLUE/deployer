<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use Illuminate\Http\Request;
use REBELinBLUE\Deployer\Contracts\Repositories\ServerRepositoryInterface;
use REBELinBLUE\Deployer\Http\Requests;
use REBELinBLUE\Deployer\Http\Requests\StoreServerRequest;

/**
 * Server management controller.
 */
class ServerController extends ResourceController
{
    /**
     * Class constructor.
     *
     * @param  ServerRepositoryInterface $repository
     * @return void
     */
    public function __construct(ServerRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Store a newly created server in storage.
     *
     * @param  StoreServerRequest $request
     * @return \Illuminate\View\View
     */
    public function store(StoreServerRequest $request)
    {
        return $this->repository->create($request->only(
            'name',
            'user',
            'ip_address',
            'port',
            'path',
            'project_id',
            'deploy_code',
            'add_commands'
        ));
    }

    /**
     * Update the specified server in storage.
     *
     * @param  StoreServerRequest $request
     * @return \Illuminate\View\View
     */
    public function update($server_id, StoreServerRequest $request)
    {
        return $this->repository->updateById($request->only(
            'name',
            'user',
            'ip_address',
            'port',
            'path',
            'project_id',
            'deploy_code'
        ), $server_id);
    }

    /**
     * Queues a connection test for the specified server.
     *
     * @param  int      $server_id
     * @return \Illuminate\View\View
     */
    public function test($server_id)
    {
        $this->repository->queueForTesting($server_id);

        return [
            'success' => true,
        ];
    }

    /**
     * Re-generates the order for the supplied servers.
     *
     * @param  Request  $request
     * @return \Illuminate\View\View
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
