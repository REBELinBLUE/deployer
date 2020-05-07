<?php

namespace REBELinBLUE\Deployer\Repositories\Contracts;

use REBELinBLUE\Deployer\Deployment;

interface DeploymentRepositoryInterface
{
    /**
     * @param array $fields
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $fields);

    /**
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getById(int $model_id);

    /**
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function abort(int $model_id): void;

    /**
     * @param int $project_id
     */
    public function abortQueued(int $project_id): void;

    /**
     * @param int $original
     * @param int $updated
     *
     * @return bool
     */
    public function updateStatusAll(int $original, int $updated);

    /**
     * @param int    $model_id
     * @param string $reason
     * @param array  $optional
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function rollback(int $model_id, string $reason = '', array $optional = []);

    /**
     * @param int $project_id
     * @param int $paginate
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLatest(int $project_id, int $paginate = 15);

    /**
     * @param int $project_id
     *
     * @return Deployment
     */
    public function getLatestSuccessful(int $project_id): ?Deployment;

    /**
     * @param int $project_id
     *
     * @return int
     */
    public function getTodayCount(int $project_id): int;

    /**
     * @param int $project_id
     *
     * @return int
     */
    public function getLastWeekCount(int $project_id): int;

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTimeline();

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPending();

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRunning();
}
