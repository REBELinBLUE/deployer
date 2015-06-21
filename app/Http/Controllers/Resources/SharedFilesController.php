<?php

namespace App\Http\Controllers\Resources;

use App\Http\Requests\StoreSharedFileRequest;
use App\Repositories\Contracts\SharedFileRepositoryInterface;

/**
 * Controller for managing files.
 */
class SharedFilesController extends ResourceController
{
    /**
     * The shared file repository.
     *
     * @var SharedFilelRepositoryInterface
     */
    private $sharedFileRepository;

    /**
     * Class constructor.
     *
     * @param  SharedFilelRepositoryInterface $sharedFileRepository
     * @return void
     */
    public function __construct(SharedFileRepositoryInterface $sharedFileRepository)
    {
        $this->sharedFileRepository = $sharedFileRepository;
    }

    /**
     * Store a newly created file in storage.
     *
     * @param  StoreSharedFileRequest $request
     * @return Response
     */
    public function store(StoreSharedFileRequest $request)
    {
        return $this->sharedFileRepository->create($request->only(
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
        return $this->sharedFileRepository->updateById($request->only(
            'name',
            'file'
        ), $file_id);
    }

    /**
     * Remove the specified file from storage.
     *
     * @param  int      $file_id
     * @return Response
     */
    public function destroy($file_id)
    {
        $this->sharedFileRepository->deleteById($file_id);

        return [
            'success' => true,
        ];
    }
}
