<?php

namespace App\Http\Controllers\Resources;

use App\Http\Requests;
use App\Http\Requests\StoreServerRequest;
use App\Jobs\TestServerConnection;
use App\Server;
use Input;
use Response;

/**
 * Server management controller.
 */
class ServerController extends ResourceController
{
    /**
     * Store a newly created server in storage.
     *
     * @param StoreServerRequest $request
     * @return Response
     */
    public function store(StoreServerRequest $request)
    {
        // fixme: use a repository
        $max = Server::where('project_id', $request->project_id)
                      ->orderBy('order', 'desc')
                      ->first();

        $order = 0;
        if (isset($max)) {
            $order = $max->order + 1;
        }

        $fields = $request->only(
            'name',
            'user',
            'ip_address',
            'port',
            'path',
            'project_id',
            'deploy_code'
        );

        $fields['order'] = $order;

        $server = Server::create($fields);

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
     * @param Server $server
     * @param StoreServerRequest $request
     * @return Response
     */
    public function update(Server $server, StoreServerRequest $request)
    {
        $server->update($request->only(
            'name',
            'user',
            'ip_address',
            'port',
            'path',
            'project_id',
            'deploy_code'
        ));

        return $server;
    }

    /**
     * Remove the specified server from storage.
     *
     * @param Server $server
     * @return Response
     */
    public function destroy(Server $server)
    {
        $server->delete();

        return [
            'success' => true,
        ];
    }

    /**
     * Queues a connection test for the specified server.
     *
     * @param Server $server
     * @return Response
     */
    public function test(Server $server)
    {
        if (!$server->isTesting()) {
            $server->status = Server::TESTING;
            $server->save();

            $this->dispatch(new TestServerConnection($server));
        }

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
            $server = Server::findOrFail($server_id);

            $server->order = $order;

            $server->save();

            $order++;
        }

        return [
            'success' => true,
        ];
    }
}
