<?php

namespace App\Http\Controllers\Resources;

use App\Http\Requests\StoreProjectFileRequest;
use App\Repositories\Contracts\ProjectFileRepositoryInterface;

/**
 * Manage the project global file like some environment files.
 */
class ProjectFileController extends ResourceController
{
    /**
     * Class constructor.
     *
     * @param  ProjectFileRepositoryInterface $repository
     * @return void
     */
    public function __construct(ProjectFileRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(StoreProjectFileRequest $request)
    {
        return $this->repository->create($request->only(
            'name',
            'path',
            'content',
            'project_id'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int      $file_id
     * @return Response
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
