<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use Illuminate\Contracts\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;

/**
 * Generic Controller class.
 */
trait ResourceController
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
     * @param int             $model_id
     * @param ResponseFactory $response
     *
     * @return array
     */
    public function destroy($model_id, ResponseFactory $response)
    {
        $this->repository->deleteById($model_id);

        return $response->json([
            'success' => true,
        ], Response::HTTP_NO_CONTENT);
    }
}
