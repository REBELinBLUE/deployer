<?php

namespace App\Http\Controllers\Resources;

use App\Http\Requests\StoreNotifyEmailRequest;
use App\Repositories\Contracts\NotifyEmailRepositoryInterface;

/**
 * Controller for managing NotifyEmails.
 */
class NotifyEmailController extends ResourceController
{
    /**
     * The email notification repository.
     *
     * @var NotificationRepositoryInterface
     */
    private $notifyEmailRepository;

    /**
     * Class constructor.
     *
     * @param  NotifyEmailRepositoryInterface $notifyEmailRepository
     * @return void
     */
    public function __construct(NotifyEmailRepositoryInterface $notifyEmailRepository)
    {
        $this->notifyEmailRepository = $notifyEmailRepository;
    }

    /**
     * Store a newly created NotifyEmail in storage.
     *
     * @param  StoreNotifyEmailRequest $request
     * @return Response
     */
    public function store(StoreNotifyEmailRequest $request)
    {
        return $this->notifyEmailRepository->create($request->only(
            'name',
            'email',
            'project_id'
        ));
    }

    /**
     * Update the specified NotifyEmail in storage.
     *
     * @param  int                     $email_id
     * @param  StoreNotifyEmailRequest $request
     * @return Response
     */
    public function update($email_id, StoreNotifyEmailRequest $request)
    {
        return $this->notifyEmailRepository->updateById($request->only(
            'name',
            'email'
        ), $email_id);
    }

    /**
     * Remove the specified NotifyEmail from storage.
     *
     * @param  int      $email_id
     * @return Response
     */
    public function destroy($email_id)
    {
        $this->notifyEmailRepository->deleteById($email_id);

        return [
            'success' => true,
        ];
    }
}
