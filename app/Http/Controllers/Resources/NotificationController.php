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
     * The notification repository.
     *
     * @var NotificationRepositoryInterface
     */
    private $slackRepository;

    /**
     * Class constructor.
     *
     * @param  NotificationRepositoryInterface $slackRepository
     * @return void
     */
    public function __construct(NotificationRepositoryInterface $slackRepository)
    {
        $this->slackRepository = $slackRepository;
    }

    /**
     * Store a newly created notification in storage.
     *
     * @param  StoreNotificationRequest $request
     * @return Response
     */
    public function store(StoreNotificationRequest $request)
    {
        return $this->slackRepository->create($request->only(
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
        return $this->slackRepository->updateById($request->only(
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
        $this->slackRepository->deleteById($notification_id);

        return [
            'success' => true,
        ];
    }
}
