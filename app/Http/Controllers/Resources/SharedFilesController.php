<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use REBELinBLUE\Deployer\Http\Controllers\Controller;
use REBELinBLUE\Deployer\Http\Requests\StoreSharedFileRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\SharedFileRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for managing files.
 */
class SharedFilesController extends Controller
{
    use ResourceController;

    /**
     * SharedFilesController constructor.
     *
     * @param SharedFileRepositoryInterface $repository
     */
    public function __construct(SharedFileRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Store a newly created file in storage.
     *
     * @param StoreSharedFileRequest $request
     * @param ResponseFactory        $response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreSharedFileRequest $request, ResponseFactory $response): JsonResponse
    {
        return $response->json($this->repository->create($request->only(
            'name',
            'file',
            'target_type',
            'target_id'
        )), Response::HTTP_CREATED);
    }

    /**
     * Update the specified file in storage.
     *
     * @param int                    $file_id
     * @param StoreSharedFileRequest $request
     *
     * @return Model
     */
    public function update(int $file_id, StoreSharedFileRequest $request): Model
    {
        return $this->repository->updateById($request->only(
            'name',
            'file'
        ), $file_id);
    }
}
