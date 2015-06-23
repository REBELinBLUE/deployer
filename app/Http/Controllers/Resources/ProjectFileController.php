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
     * The project file repository.
     *
     * @var ProjectFileRepositoryInterface
     */
    private $fileRepository;

    /**
     * Class constructor.
     *
     * @param  ProjectFileRepositoryInterface $fileRepository
     * @return void
     */
    public function __construct(ProjectFileRepositoryInterface $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(StoreProjectFileRequest $request)
    {
        return $this->fileRepository->create($request->only(
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
        return $this->fileRepository->updateById($request->only(
            'name',
            'path',
            'content'
        ), $file_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int      $file_id
     * @return Response
     */
    public function destroy($file_id)
    {
        $this->fileRepository->deleteById($file_id);

        return [
            'success' => true,
        ];
    }
}
