<?php

namespace App\Repositories;

use App\Repositories\Contracts\SharedFileRepositoryInterface;
use App\Repositories\EloquentRepository;
use App\SharedFile;

/**
 * The shared file repository.
 */
class EloquentSharedFileRepository extends EloquentRepository implements SharedFileRepositoryInterface
{
    /**
     * Class constructor.
     *
     * @param  SharedFile                    $model
     * @return SharedFileRepositoryInterface
     */
    public function __construct(SharedFile $model)
    {
        $this->model = $model;
    }
}
