<?php

namespace REBELinBLUE\Deployer\Repositories\Contracts;

use Carbon\Carbon;

interface ProjectRepositoryInterface
{
    /**
     * @param string $hash
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getByHash($hash);

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll();

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
    public function updateById(array $fields, $model_id);

    /**
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return bool
     */
    public function deleteById($model_id);

    /**
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return array
     */
    public function refreshBranches($model_id);

    /**
     * @param int      $count
     * @param callable $callback
     *
     * @return bool
     */
    public function chunk($count, callable $callback);

    /**
     * @param int $original
     * @param int $updated
     *
     * @return bool
     */
    public function updateStatusAll($original, $updated);

    /**
     * @param Carbon   $last_mirrored_since
     * @param int      $count
     * @param callable $callback
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLastMirroredBefore(Carbon $last_mirrored_since, $count, callable $callback);
}
