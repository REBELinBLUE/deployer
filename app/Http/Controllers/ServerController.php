<?php namespace App\Http\Controllers;

use Response;
use Queue;
use App\Server;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Commands\TestServerConnection;
use App\Http\Requests\StoreServerRequest;

/**
 * Server management controller
 */
class ServerController extends Controller
{
    /**
     * Shows the specified server
     *
     * @param int $server_id
     * @return Response
     */
    public function show($server_id)
    {
        return Server::findOrFail($server_id);
    }

    /**
     * Store a newly created server in storage.
     *
     * @param StoreServerRequest $request
     * @return Response
     */
    public function store(StoreServerRequest $request)
    {
        $server = new Server;
        $server->name       = $request->name;
        $server->user       = $request->user;
        $server->ip_address = $request->ip_address;
        $server->path       = $request->path;
        $server->project_id = $request->project_id;
        $server->status     = Server::UNTESTED;
        $server->save();

        return $server;
    }

    /**
     * Update the specified server in storage.
     *
     * @param int $server_id
     * @param StoreServerRequest $request
     * @return Response
     */
    public function update($server_id, StoreServerRequest $request)
    {
        $server = Server::findOrFail($server_id);

        if ($server->ip_address != $request->ip_address) {
            $server->status = Server::UNTESTED;
        }

        $server->name       = $request->name;
        $server->user       = $request->user;
        $server->ip_address = $request->ip_address;
        $server->path       = $request->path;
        $server->project_id = $request->project_id;
        $server->save();

        return $server;
    }

    /**
     * Remove the specified server from storage.
     *
     * @param int $server_id
     * @return Response
     */
    public function destroy($server_id)
    {
        $server = Server::findOrFail($server_id);
        $server->delete();

        return Response::json([
            'success' => true
        ], 200);
    }

    /**
     * Queues a connection test for the specified server
     *
     * @param int $server_id
     * @return Response
     */
    public function test($server_id)
    {
        $server = Server::findOrFail($server_id);

        $server->status = Server::TESTING;
        $server->save();

        Queue::pushOn('connections', new TestServerConnection($server));

        return Response::json([
            'success' => true
        ], 200);
    }
}
