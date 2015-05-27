<?php namespace App\Http\Controllers;

use App\SharedFile;
use App\Http\Requests\StoreSharedFileRequest;

/**
 * Controller for managing files
 */
class SharedFilesController extends ResourceController
{
    /**
     * Store a newly created file in storage.
     *
     * @param StoreSharedFileRequest $request
     * @return Response
     */
    public function store(StoreSharedFileRequest $request)
    {
        return SharedFile::create($request->only(
            'name',
            'file',
            'project_id'
        ));
    }

    /**
     * Update the specified file in storage.
     *
     * @param SharedFile $sharedFile
     * @param StoreSharedFileRequest $request
     * @return Response
     */
    public function update(SharedFile $file, StoreSharedFileRequest $request)
    {
        $file->update($request->only(
            'name',
            'file'
        ));

        return $file;
    }

    /**
     * Remove the specified file from storage.
     *
     * @param SharedFile $sharedFile
     * @return Response
     */
    public function destroy(SharedFile $file)
    {
        $file->delete();

        return [
            'success' => true
        ];
    }
}
