<?php namespace App\Http\Controllers;

use Response;
use Queue;
use App\Server;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Commands\TestServerConnection;
use App\Http\Requests\StoreServerRequest;

class ServerController extends Controller
{
    public function show($server_id)
    {
        return Server::findOrFail($server_id);
    }

    /**
     * Store a newly created resource in storage.
     *
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
     * Update the specified resource in storage.
     *
     * @param  int  $id
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
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
