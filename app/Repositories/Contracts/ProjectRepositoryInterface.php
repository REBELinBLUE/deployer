<?php

namespace REBELinBLUE\Deployer\Repositories\Contracts;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use REBELinBLUE\Deployer\Project;

interface ProjectRepositoryInterface
{
    /**
     * @param string $hash
     *
     * @return Project
     */
    public function getByHash(string $hash): Project;

    /**
     * @param bool $with_user
     *
     * @return Collection
     */
    public function getAll(bool $with_user = false);

    /**
     * @param array $fields
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $fields);

    /**
     * @param array $fields
     * @param int   $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateById(array $fields, int $model_id);

    /**
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return bool
     */
    public function deleteById(int $model_id);

    /**
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return array
     */
    public function refreshBranches(int $model_id);

    /**
     * @param int      $count
     * @param callable $callback
     *
     * @return bool
     */
    public function chunk(int $count, callable $callback);

    /**
     * @param int $original
     * @param int $updated
     *
     * @return bool
     */
    public function updateStatusAll(int $original, int $updated);

    /**
     * @param Carbon   $last_mirrored_since
     * @param int      $count
     * @param callable $callback
     *
     * @return Collection
     */
    public function getLastMirroredBefore(Carbon $last_mirrored_since, int $count, callable $callback);
}
