<?php

namespace REBELinBLUE\Deployer\Repositories;

use Illuminate\Foundation\Bus\DispatchesJobs;
use REBELinBLUE\Deployer\Jobs\TestServerConnection;
use REBELinBLUE\Deployer\ProjectServer;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectServerRepositoryInterface;

/**
 * The project server repository.
 */
class EloquentProjectServerRepository extends EloquentRepository implements ProjectServerRepositoryInterface
{
    use DispatchesJobs;

    /**
     * EloquentProjectServerRepository constructor.
     *
     * @param ProjectServer $model
     */
    public function __construct(ProjectServer $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $project_server_id
     */
    public function queueForTesting($project_server_id)
    {
        $server = $this->getById($project_server_id);

        if (!$server->isTesting()) {
            $server->status = ProjectServer::TESTING;
            $server->save();

            $this->dispatch(new TestServerConnection($server));
        }
    }
}
