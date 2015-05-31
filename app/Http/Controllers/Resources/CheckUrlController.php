<?php namespace App\Http\Controllers\Resources;

use App\CheckUrl;
use App\Http\Requests\StoreCheckUrlRequest;

/**
 * Controller for managing URLs
 * TODO: Change create/update to queue a check of the URL
 */
class CheckUrlController extends ResourceController
{
    /**
     * Store a newly created url in storage.
     *
     * @param StoreCheckUrlRequest $request
     * @return Response
     */
    public function store(StoreCheckUrlRequest $request)
    {
        return CheckUrl::create($request->only(
            'title',
            'url',
            'is_report',
            'period',
            'project_id'
        ));
    }

    /**
     * Update the specified file in storage.
     *
     * @param CheckUrl $url
     * @param StoreCheckUrlRequest $request
     * @return Response
     */
    public function update(CheckUrl $url, StoreCheckUrlRequest $request)
    {
        $url->update($request->only(
            'title',
            'url',
            'is_report',
            'period'
        ));

        return $url;
    }

    /**
     * Remove the specified url from storage.
     *
     * @param CheckUrl $url
     * @return Response
     */
    public function destroy(CheckUrl $url)
    {
        $url->delete();

        return [
            'success' => true
        ];
    }
}
