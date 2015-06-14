<?php

namespace App\Http\Controllers\Resources;

use App\Http\Requests;
use App\Http\Requests\StoreServerRequest;
use App\Jobs\TestServerConnection;
use App\Server;
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
        return Server::create($request->only(
            'name',
            'user',
            'ip_address',
            'port',
            'path',
            'project_id',
            'deploy_code'
        ));
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
            'success' => true
        ];
    }

    /**
     * Queues a connection test for the specified server.
     *
     * @param Server $server
     * @return Response
     * TODO: Shouldn't changing the status to testing automatically add the TestServerConnect to the queue on save?
     */
    public function test(Server $server)
    {
        if ($server->status !== Server::TESTING) { // FIXME: Move to a method on the Server
            $server->status = Server::TESTING;
            $server->save();

            $this->dispatch(new TestServerConnection($server));
        }

        return [
            'success' => true
        ];
    }
}
