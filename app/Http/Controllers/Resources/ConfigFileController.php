<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use REBELinBLUE\Deployer\Http\Requests\StoreConfigFileRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\ConfigFileRepositoryInterface;

/**
 * Manage the configuration files for a project.
 */
class ConfigFileController extends ResourceController
{
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
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(StoreConfigFileRequest $request)
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
