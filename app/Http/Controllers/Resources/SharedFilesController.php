<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use REBELinBLUE\Deployer\Http\Requests\StoreSharedFileRequest;
use REBELinBLUE\Deployer\Contracts\Repositories\SharedFileRepositoryInterface;

/**
 * Controller for managing files.
 */
class SharedFilesController extends ResourceController
{
    /**
     * Class constructor.
     *
     * @param  SharedFileRepositoryInterface $repository
     * @return void
     */
    public function __construct(SharedFileRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Store a newly created file in storage.
     *
     * @param  StoreSharedFileRequest $request
     * @return Response
     */
    public function store(StoreSharedFileRequest $request)
    {
        return $this->repository->create($request->only(
            'name',
            'file',
            'project_id'
        ));
    }

    /**
     * Update the specified file in storage.
     *
     * @param  int                    $file_id
     * @param  StoreSharedFileRequest $request
     * @return Response
     */
    public function update($file_id, StoreSharedFileRequest $request)
    {
        return $this->repository->updateById($request->only(
            'name',
            'file'
        ), $file_id);
    }
}
