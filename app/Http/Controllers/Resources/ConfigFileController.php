<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use Illuminate\Contracts\Routing\ResponseFactory;
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreConfigFileRequest $request, ResponseFactory $response)
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
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($file_id, StoreConfigFileRequest $request)
    {
        return $this->repository->updateById($request->only(
            'name',
            'path',
            'content'
        ), $file_id);
    }
}
