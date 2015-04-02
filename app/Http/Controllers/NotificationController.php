<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator;
use Input;
use Response;
use Queue;

use App\Commands\Notify;

use App\Notification;

class NotificationController extends Controller
{
    public function show($notification_id)
    {
        return Notification::findOrFail($notification_id);
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
            'channel'    => 'required',
            'webhook'    => 'required|url',
            'project_id' => 'required|integer|exists:projects,id'
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        } else {
            $notification = new Notification;
            
            $notification->name       = Input::get('name');
            $notification->channel    = Input::get('channel');
            $notification->webhook    = Input::get('webhook');
            $notification->project_id = Input::get('project_id');

            $notification->save();

            Queue::pushOn('notify', new Notify($notification, $notification->testPayload()));

            return $notification;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($notification_id)
    {
        $rules = array(
            'name'       => 'required',
            'channel'    => 'required',
            'webhook'    => 'required|url',
            'project_id' => 'required|integer|exists:projects,id'
        );

        $validator = Validator::make(Input::all(), $rules);

        // FIXME: Why is this a redirect?
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        } else {
            $notification = Notification::findOrFail($notification_id);

            $notification->name       = Input::get('name');
            $notification->channel    = Input::get('channel');
            $notification->webhook    = Input::get('webhook');
            $notification->project_id = Input::get('project_id');

            $notification->save();

            Queue::pushOn('notify', new Notify($notification, $notification->testPayload()));

            return $notification;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($notification_id)
    {
        $notification = Notification::findOrFail($notification_id);
        $notification->delete();

        return Response::json([
            'success' => true
        ], 200);
    }
}
