<?php

namespace App\Http\Controllers\Resources;

use App\Http\Requests;
use App\Http\Requests\StoreServerRequest;
use App\Repositories\Contracts\ServerRepositoryInterface;
use Input;

/**
 * Server management controller.
 */
class ServerController extends ResourceController
{
    /**
     * The group repository.
     *
     * @var ServerRepositoryInterface
     */
    private $serverRepository;

    /**
     * Class constructor.
     *
     * @param  ServerRepositoryInterface $serverRepository
     * @return void
     */
    public function __construct(ServerRepositoryInterface $serverRepository)
    {
        $this->serverRepository = $serverRepository;
    }

    /**
     * Store a newly created server in storage.
     *
     * @param  StoreServerRequest $request
     * @return Response
     */
    public function store(StoreServerRequest $request)
    {
        $server = $this->serverRepository->create($request->only(
            'name',
            'user',
            'ip_address',
            'port',
            'path',
            'project_id',
            'deploy_code'
        ));

        // Add the server to the existing commands
        if ($request->has('add_commands') && $request->add_commands === true) {
            foreach ($server->project->commands as $command) {
                $command->servers()->attach($server->id);
            }
        }

        return $server;
    }

    /**
     * Update the specified server in storage.
     *
     * @param  StoreServerRequest $request
     * @return Response
     */
    public function update($server_id, StoreServerRequest $request)
    {
        return $this->serverRepository->updateById($request->only(
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
     * Remove the specified server from storage.
     *
     * @param  int      $server
     * @return Response
     */
    public function destroy($server_id)
    {
        $this->serverRepository->deleteById($server_id);

        return [
            'success' => true,
        ];
    }

    /**
     * Queues a connection test for the specified server.
     *
     * @param  int      $server_id
     * @return Response
     */
    public function test($server_id)
    {
        $this->serverRepository->queueForTesting($server_id);

        return [
            'success' => true,
        ];
    }

    /**
     * Re-generates the order for the supplied servers.
     *
     * @return Response
     */
    public function reorder()
    {
        $order = 0;

        foreach (Input::get('servers') as $server_id) {
            $server = $this->serverRepository->updateById([
                'order' => $order,
            ], $server_id);

            $order++;
        }

        return [
            'success' => true,
        ];
    }
}
