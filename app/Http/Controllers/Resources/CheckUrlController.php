<?php

namespace App\Http\Controllers\Resources;

use App\CheckUrl;
use App\Http\Requests\StoreCheckUrlRequest;
use App\Jobs\RequestProjectCheckUrl;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Controller for managing URLs.
 */
class CheckUrlController extends ResourceController
{
    use DispatchesJobs;

    /**
     * Store a newly created url in storage.
     *
     * @param  StoreCheckUrlRequest $request
     * @return Response
     */
    public function store(StoreCheckUrlRequest $request)
    {
        $url = CheckUrl::create($request->only(
            'title',
            'url',
            'is_report',
            'period',
            'project_id'
        ));

        $this->dispatch(new RequestProjectCheckUrl([$url])); // FIXME: Move this to model events

        return $url;
    }

    /**
     * Update the specified file in storage.
     *
     * @param  CheckUrl             $url
     * @param  StoreCheckUrlRequest $request
     * @return Response
     */
    public function update(CheckUrl $url, StoreCheckUrlRequest $request)
    {
        $old_url = $url->url;

        $url->update($request->only(
            'title',
            'url',
            'is_report',
            'period'
        ));

        if ($old_url !== $url->url) {
            $this->dispatch(new RequestProjectCheckUrl([$url]));
        }

        return $url;
    }

    /**
     * Remove the specified url from storage.
     *
     * @param  CheckUrl $url
     * @return Response
     */
    public function destroy(CheckUrl $url)
    {
        $url->delete();

        return [
            'success' => true,
        ];
    }
}
