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
    private $projectFileRepository;

    /**
     * Class constructor.
     *
     * @param  ProjectFileRepositoryInterface $projectFileRepository
     * @return void
     */
    public function __construct(ProjectFileRepositoryInterface $projectFileRepository)
    {
        $this->projectFileRepository = $projectFileRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(StoreProjectFileRequest $request)
    {
        return $this->projectFileRepository->create($request->only(
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
        return $this->projectFileRepository->updateById($request->only(
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
        $this->projectFileRepository->deleteById($file_id);

        return [
            'success' => true,
        ];
    }
}
