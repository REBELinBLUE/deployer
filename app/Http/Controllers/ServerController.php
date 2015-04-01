<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator;
use Input;
use Response;
use Queue;
use App\Commands\TestServerConnection;

use App\Server;
use App\Project;

class ServerController extends Controller
{
    public function show($server_id)
    {
        return Server::findOrFail($server_id);
    }

    public function test($server_id)
    {
        $server = Server::findOrFail($server_id);

        $server->status = 'Testing';
        $server->save();

        Queue::pushOn('connections', new TestServerConnection($server));

        return Response::json([
            'success' => true
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $rules = array(
            'name'       => 'required',
            'user'       => 'required',
            'ip_address' => 'required|ip',
            'path'       => 'required',
            'project_id' => 'required|integer|exists:projects,id'
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        } else {
            $server = new Server;
            $server->name       = Input::get('name');
            $server->user       = Input::get('user');
            $server->ip_address = Input::get('ip_address');
            $server->path       = Input::get('path');
            $server->project_id = Input::get('project_id');
            $server->status     = 'Untested';
            $server->save();

            return $server;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($server_id)
    {
        $rules = array(
            'name'       => 'required',
            'user'       => 'required',
            'ip_address' => 'required|ip',
            'path'       => 'required',
            'project_id' => 'required|integer|exists:projects,id'
        );

        $validator = Validator::make(Input::all(), $rules);

        // FIXME: Why is this a redirect?
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        } else {
            $server = Server::findOrFail($server_id);

            if ($server->ip_address != Input::get('ip_address')) {
                $server->status = 'Untested';
            }

            $server->name       = Input::get('name');
            $server->user       = Input::get('user');
            $server->ip_address = Input::get('ip_address');
            $server->path       = Input::get('path');
            $server->project_id = Input::get('project_id');
            $server->save();

            return $server;
        }
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
}
