<?php namespace App\Http\Controllers;

use Queue;
use App\Notification;
use App\Commands\Notify;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNotificationRequest;

/**
 * Controller for managing notifications
 */
class NotificationController extends Controller
{
    /**
     * Show the specified notification
     *
     * @param int $notification_id
     * @return Response
     */
    public function show($notification_id)
    {
        return Notification::findOrFail($notification_id);
    }

    /**
     * Store a newly created notification in storage.
     *
     * @param StoreNotificationRequest $request
     * @return Response
     */
    public function store(StoreNotificationRequest $request)
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
     * Update the specified notification in storage.
     *
     * @param int $notification_id
     * @param StoreNotificationRequest $request
     * @return Response
     */
    public function update($notification_id, StoreNotificationRequest $request)
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
     * Remove the specified notification from storage.
     *
     * @param int $notification_id
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
