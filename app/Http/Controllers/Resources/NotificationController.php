<?php

namespace App\Http\Controllers\Resources;

use App\Http\Requests\StoreNotificationRequest;
use App\Repositories\Contracts\NotificationRepositoryInterface;

/**
 * Controller for managing notifications.
 */
class NotificationController extends ResourceController
{
    /**
     * The user repository.
     *
     * @var NotificationRepositoryInterface
     */
    private $notificationRepository;

    /**
     * Class constructor.
     *
     * @param  NotificationRepositoryInterface $notificationRepository
     * @return void
     */
    public function __construct(NotificationRepositoryInterface $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * Store a newly created notification in storage.
     *
     * @param  StoreNotificationRequest $request
     * @return Response
     */
    public function store(StoreNotificationRequest $request)
    {
        return $this->notificationRepository->create($request->only(
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
     * @param  int                      $notification_id
     * @param  StoreNotificationRequest $request
     * @return Response
     */
    public function update($notification_id, StoreNotificationRequest $request)
    {
        return $this->notificationRepository->updateById($request->only(
            'name',
            'channel',
            'webhook',
            'icon',
            'failure_only'
        ), $notification_id);
    }

    /**
     * Remove the specified notification from storage.
     *
     * @param  int      $notification_id
     * @return Response
     */
    public function destroy($notification_id)
    {
        $this->notificationRepository->deleteById($notification_id);

        return [
            'success' => true,
        ];
    }
}
