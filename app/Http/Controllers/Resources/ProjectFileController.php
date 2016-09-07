<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use REBELinBLUE\Deployer\Contracts\Repositories\ProjectFileRepositoryInterface;
use REBELinBLUE\Deployer\Http\Requests\StoreProjectFileRequest;

/**
 * Manage the project global file like some environment files.
 */
class ProjectFileController extends ResourceController
{
    /**
     * ProjectFileController constructor.
     *
     * @param ProjectFileRepositoryInterface $repository
     */
    public function __construct(ProjectFileRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProjectFileRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(StoreProjectFileRequest $request)
    {
        return $this->repository->create($request->only(
            'name',
            'path',
            'content',
            'target_type',
            'target_id'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $file_id
     * @param StoreProjectFileRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($file_id, StoreProjectFileRequest $request)
    {
        return $this->repository->updateById($request->only(
            'name',
            'path',
            'content'
        ), $file_id);
    }
}
