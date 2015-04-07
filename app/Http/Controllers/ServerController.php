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
     * @param Server $server
     * @return Response
     */
    public function show(Server $server)
    {
        return $server;
    }

    /**
     * Store a newly created server in storage.
     *
     * @param StoreServerRequest $request
     * @return Response
     * @todo Use mass assignment if possible
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
     * @param Server $server
     * @param StoreServerRequest $request
     * @return Response
     * @todo Use mass assignment if possible
     */
    public function update(Server $server, StoreServerRequest $request)
    {
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
     * @param Server $server
     * @return Response
     */
    public function destroy(Server $server)
    {
        $server->delete();

        return [
            'success' => true
        ];
    }

    /**
     * Queues a connection test for the specified server
     *
     * @param Server $server
     * @return Response
     */
    public function test(Server $server)
    {
        $server->status = Server::TESTING;
        $server->save();

        Queue::pushOn('connections', new TestServerConnection($server));

        return [
            'success' => true
        ];
    }
}
