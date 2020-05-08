<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use REBELinBLUE\Deployer\Http\Controllers\Controller;
use REBELinBLUE\Deployer\Http\Requests\StoreConfigFileRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\ConfigFileRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manage the configuration files for a project.
 */
class ConfigFileController extends Controller
{
    use ResourceController;

    /**
     * ConfigFileController constructor.
     *
     * @param ConfigFileRepositoryInterface $repository
     */
    public function __construct(ConfigFileRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreConfigFileRequest $request
     * @param ResponseFactory        $response
     *
     * @return JsonResponse
     */
    public function store(StoreConfigFileRequest $request, ResponseFactory $response): JsonResponse
    {
        return $response->json($this->repository->create($request->only(
            'name',
            'path',
            'content',
            'target_type',
            'target_id'
        )), Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int                    $file_id
     * @param StoreConfigFileRequest $request
     *
     * @return Model
     */
    public function update(int $file_id, StoreConfigFileRequest $request): Model
    {
        return $this->repository->updateById($request->only(
            'name',
            'path',
            'content'
        ), $file_id);
    }
}
