<?php namespace App\Http\Controllers;

use Response;
use Queue;
use App\Server;
use App\Http\Requests;
use App\Commands\TestServerConnection;
use App\Http\Requests\StoreServerRequest;

/**
 * Server management controller
 */
class ServerController extends ResourceController
{
    /**
     * Shows the specified server
     *
     * @param Server $server
     * @return Response
     * TODO: Check this is used
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
     * TODO: Shouldn't changing the status on IP change be up to the model not the controller?
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
     * Queues a connection test for the specified server
     *
     * @param Server $server
     * @return Response
     * TODO: Shouldn't changing the status to testing automatically add the model to the queue on save?
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
