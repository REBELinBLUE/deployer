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
    protected $repository;

    /**
     * Class constructor.
     *
     * @param  CheckUrlRepositoryInterface $repository
     * @return void
     */
    public function __construct(CheckUrlRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Store a newly created URL in storage.
     *
     * @param  StoreCheckUrlRequest $request
     * @return Response
     */
    public function store(StoreCheckUrlRequest $request)
    {
        return $this->repository->create($request->only(
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
        return $this->repository->updateById($request->only(
            'title',
            'url',
            'is_report',
            'period'
        ), $url_id);
    }
}
