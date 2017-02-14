<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use REBELinBLUE\Deployer\Http\Controllers\Controller;
use REBELinBLUE\Deployer\Http\Requests\StoreVariableRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\VariableRepositoryInterface;

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
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(StoreVariableRequest $request)
    {
        return $this->repository->create($request->only(
            'name',
            'value',
            'target_type',
            'target_id'
        ));
    }

    /**
     * Update the specified variable in storage.
     *
     * @param int                  $variable_id
     * @param StoreVariableRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($variable_id, StoreVariableRequest $request)
    {
        return $this->repository->updateById($request->only(
            'name',
            'value'
        ), $variable_id);
    }
}
