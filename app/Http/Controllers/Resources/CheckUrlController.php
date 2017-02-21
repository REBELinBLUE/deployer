<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use Illuminate\Contracts\Routing\ResponseFactory;
use REBELinBLUE\Deployer\Http\Controllers\Controller;
use REBELinBLUE\Deployer\Http\Requests\StoreCheckUrlRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\CheckUrlRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for managing URLs.
 */
class CheckUrlController extends Controller
{
    use ResourceController;

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
     * @param ResponseFactory      $response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCheckUrlRequest $request, ResponseFactory $response)
    {
        return $response->json($this->repository->create($request->only(
            'name',
            'url',
            'period',
            'project_id'
        )), Response::HTTP_CREATED);
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
