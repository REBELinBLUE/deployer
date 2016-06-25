<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use REBELinBLUE\Deployer\Http\Controllers\Controller;

/**
 * Generic Controller class.
 */
abstract class ResourceController extends Controller
{
    /**
     * The model repository.
     *
     * @var \REBELinBLUE\Deployer\Repositories\EloquentRepository
     */
    protected $repository;

    /**
     * Remove the specified model from storage.
     *
     * @param int $model_id
     * @return array
     */
    public function destroy($model_id)
    {
        $this->repository->deleteById($model_id);

        return [
            'success' => true,
        ];
    }
}
