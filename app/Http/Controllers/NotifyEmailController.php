<?php namespace App\Http\Controllers;

use App\NotifyEmail;
use App\Http\Requests\StoreNotifyEmailRequest;

/**
 * Controller for managing NotifyEmails
 */
class NotifyEmailController extends ResourceController
{
    /**
     * Store a newly created notify_email in storage.
     *
     * @param StoreNotifyEmailRequest $request
     * @return Response
     */
    public function store(StoreNotifyEmailRequest $request)
    {
        return NotifyEmail::create($request->only(
            'name',
            'email',
            'project_id'
        ));
    }

    /**
     * Update the specified notify_email in storage.
     *
     * @param NotifyEmail $notify_email
     * @param StoreNotifyEmailRequest $request
     * @return Response
     */
    public function update(NotifyEmail $notify_email, StoreNotifyEmailRequest $request)
    {
        $notify_email->update($request->only(
            'name',
            'email'
        ));

        return $notify_email;
    }

    /**
     * Remove the specified NotifyEmail from storage.
     *
     * @param NotifyEmail $notify_email
     * @return Response
     */
    public function destroy(NotifyEmail $notify_email)
    {
        $notify_email->delete();

        return [
            'success' => true
        ];
    }
}
