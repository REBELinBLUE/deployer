<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use REBELinBLUE\Deployer\Http\Controllers\Controller;
use REBELinBLUE\Deployer\Http\Requests\StoreVariableRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\VariableRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Variable management controller.
 */
class VariableController extends Controller
{
    use ResourceController;

    /**
     * VariableController constructor.
     *
     * @param VariableRepositoryInterface $repository
     */
    public function __construct(VariableRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Store a newly created variable in storage.
     *
     * @param StoreVariableRequest $request
     * @param ResponseFactory      $response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreVariableRequest $request, ResponseFactory $response): JsonResponse
    {
        return $response->json($this->repository->create($request->only(
            'name',
            'value',
            'target_type',
            'target_id'
        )), Response::HTTP_CREATED);
    }

    /**
     * Update the specified variable in storage.
     *
     * @param int                  $variable_id
     * @param StoreVariableRequest $request
     *
     * @return Model
     */
    public function update(int $variable_id, StoreVariableRequest $request): Model
    {
        return $this->repository->updateById($request->only(
            'name',
            'value'
        ), $variable_id);
    }
}
