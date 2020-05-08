<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
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
     * @return JsonResponse
     */
    public function store(StoreCheckUrlRequest $request, ResponseFactory $response): JsonResponse
    {
        return $response->json($this->repository->create($request->only(
            'name',
            'url',
            'period',
            'project_id',
            'match'
        )), Response::HTTP_CREATED);
    }

    /**
     * Update the specified URL in storage.
     *
     * @param int                  $url_id
     * @param StoreCheckUrlRequest $request
     *
     * @return Model
     */
    public function update(int $url_id, StoreCheckUrlRequest $request): Model
    {
        return $this->repository->updateById($request->only(
            'name',
            'url',
            'period',
            'match'
        ), $url_id);
    }
}
