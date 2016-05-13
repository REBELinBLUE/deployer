<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use REBELinBLUE\Deployer\Http\Requests;
use REBELinBLUE\Deployer\Http\Requests\StoreVariableRequest;
use REBELinBLUE\Deployer\Contracts\Repositories\VariableRepositoryInterface;

/**
 * Variable management controller.
 */
class VariableController extends ResourceController
{
    /**
     * Class constructor.
     *
     * @param  VariableRepositoryInterface $repository
     * @return void
     */
    public function __construct(VariableRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Store a newly created variable in storage.
     *
     * @param  StoreVariableRequest $request
     * @return Response
     */
    public function store(StoreVariableRequest $request)
    {
        return $this->repository->create($request->only(
            'name',
            'value',
            'project_id'
        ));
    }

    /**
     * Update the specified variable in storage.
     *
     * @param  int                  $variable_id
     * @param  StoreVariableRequest $request
     * @return Response
     */
    public function update($variable_id, StoreVariableRequest $request)
    {
        return $this->repository->updateById($request->only(
            'name',
            'value',
            'project_id'
        ), $variable_id);
    }
}
