<?php

namespace App\Http\Controllers\Resources;

use App\Http\Requests\StoreCheckUrlRequest;
use App\Repositories\Contracts\CheckUrlRepositoryInterface;

/**
 * Controller for managing URLs.
 */
class CheckUrlController extends ResourceController
{
    /**
     * The CheckURL repository.
     *
     * @var CheckUrlRepositoryInterface
     */
    private $checkurlRepository;

    /**
     * Class constructor.
     *
     * @param  CheckUrlRepositoryInterface $checkurlRepository
     * @return void
     */
    public function __construct(CheckUrlRepositoryInterface $checkurlRepository)
    {
        $this->checkurlRepository = $checkurlRepository;
    }

    /**
     * Store a newly created URL in storage.
     *
     * @param  StoreCheckUrlRequest $request
     * @return Response
     */
    public function store(StoreCheckUrlRequest $request)
    {
        return $this->checkurlRepository->create($request->only(
            'title',
            'url',
            'is_report',
            'period',
            'project_id'
        ));
    }

    /**
     * Update the specified URL in storage.
     *
     * @param  int                  $url_id
     * @param  StoreCheckUrlRequest $request
     * @return Response
     */
    public function update($url_id, StoreCheckUrlRequest $request)
    {
        return $this->checkurlRepository->updateById($request->only(
            'title',
            'url',
            'is_report',
            'period'
        ), $url_id);
    }

    /**
     * Remove the specified URL from storage.
     *
     * @param  int      $url_id
     * @return Response
     */
    public function destroy($url_id)
    {
        $this->checkurlRepository->deleteById($url_id);

        return [
            'success' => true,
        ];
    }
}
