<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Queue;

use App\Commands\Notify;

use App\Notification;

use App\Http\Requests\NotificationRequest;

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
    public function store(NotificationRequest $request)
    {
        $notification = new Notification;
        
        $notification->name       = $request->name;
        $notification->channel    = $request->channel;
        $notification->webhook    = $request->webhook;
        $notification->project_id = $request->project_id;

        $notification->save();

        Queue::pushOn('notify', new Notify($notification, $notification->testPayload()));

        return $notification;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($notification_id, NotificationRequest $request)
    {
        $notification = Notification::findOrFail($notification_id);

        $notification->name       = $request->name;
        $notification->channel    = $request->channel;
        $notification->webhook    = $request->webhook;
        $notification->project_id = $request->project_id;

        $notification->save();

        Queue::pushOn('notify', new Notify($notification, $notification->testPayload()));

        return $notification;
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
