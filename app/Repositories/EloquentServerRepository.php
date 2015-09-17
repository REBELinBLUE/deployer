<?php

namespace REBELinBLUE\Deployer\Repositories;

use Illuminate\Foundation\Bus\DispatchesJobs;
use REBELinBLUE\Deployer\Jobs\TestServerConnection;
use REBELinBLUE\Deployer\Repositories\Contracts\ServerRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Server;

/**
 * The server repository.
 */
class EloquentServerRepository extends EloquentRepository implements ServerRepositoryInterface
{
    use DispatchesJobs;

    /**
     * Class constructor.
     *
     * @param  Server                   $model
     * @return EloquentServerRepository
     */
    public function __construct(Server $model)
    {
        $this->model = $model;
    }

    /**
     * Gets all servers.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->model
                    ->orderBy('name')
                    ->get();
    }

    /**
     * Creates a new instance of the server.
     *
     * @param  array $fields
     * @return Model
     */
    public function create(array $fields)
    {
        // Get the current highest server order
        $max = $this->model->where('project_id', $fields['project_id'])
                           ->orderBy('order', 'DESC')
                           ->first();

        $order = 0;
        if (isset($max)) {
            $order = $max->order + 1;
        }

        $fields['order'] = $order;

        $add_commands = false;
        if (isset($fields['add_commands'])) {
            $add_commands = $fields['add_commands'];
            unset($fields['add_commands']);
        }

        $server = $this->model->create($fields);

        // Add the server to the existing commands
        if ($add_commands) {
            foreach ($server->project->commands as $command) {
                $command->servers()->attach($server->id);
            }
        }

        return $server;
    }

    /**
     * Updates a server instance by it's ID and queues it for testing.
     *
     * @return Model
     */
    public function queueForTesting($server_id)
    {
        $server = $this->getById($server_id);

        if (!$server->isTesting()) {
            $server->status = Server::TESTING;
            $server->save();

            $this->dispatch(new TestServerConnection($server));
        }
    }
}
