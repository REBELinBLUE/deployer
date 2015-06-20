<?php

namespace App\Http\Controllers\Resources;

use App\Http\Requests\StoreNotificationRequest;
use App\Notification;

/**
 * Controller for managing notifications.
 */
class NotificationController extends ResourceController
{
    /**
     * Store a newly created notification in storage.
     *
     * @param  StoreNotificationRequest $request
     * @return Response
     */
    public function store(StoreNotificationRequest $request)
    {
        return Notification::create($request->only(
            'name',
            'channel',
            'webhook',
            'project_id',
            'icon',
            'failure_only'
        ));
    }

    /**
     * Update the specified notification in storage.
     *
     * @param  Notification             $notification
     * @param  StoreNotificationRequest $request
     * @return Response
     */
    public function update(Notification $notification, StoreNotificationRequest $request)
    {
        $notification->update($request->only(
            'name',
            'channel',
            'webhook',
            'icon',
            'failure_only'
        ));

        return $notification;
    }

    /**
     * Remove the specified notification from storage.
     *
     * @param  Notification $notification
     * @return Response
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();

        return [
            'success' => true,
        ];
    }
}
