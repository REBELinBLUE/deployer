<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use REBELinBLUE\Deployer\Contracts\Repositories\CheckUrlRepositoryInterface;
use REBELinBLUE\Deployer\Http\Requests\StoreCheckUrlRequest;

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
     * CheckUrlController constructor.
     *
     * @param CheckUrlRepositoryInterface $repository
     */
    public function __construct(CheckUrlRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Store a newly created URL in storage.
     *
     * @param StoreCheckUrlRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(StoreCheckUrlRequest $request)
    {
        return $this->repository->create($request->only(
            'name',
            'url',
            'period',
            'project_id'
        ));
    }

    /**
     * Update the specified URL in storage.
     *
     * @param int                  $url_id
     * @param StoreCheckUrlRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($url_id, StoreCheckUrlRequest $request)
    {
        return $this->repository->updateById($request->only(
            'name',
            'url',
            'period'
        ), $url_id);
    }
}
