<?php

namespace REBELinBLUE\Deployer\Contracts\Repositories;

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
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById($model_id);

    /**
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function abort($model_id);

    /**
     * @param int $project_id
     */
    public function abortQueued($project_id);

    /**
     * @param int $model_id
     * @param string $reason
     * @param array $optional
     *
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function rollback($model_id, $reason = '', array $optional = []);

    /**
     * @param int $project_id
     * @param int $paginate
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLatest($project_id, $paginate = 15);

    /**
     * @param int $project_id
     *
     * @return Deployment
     */
    public function getLatestSuccessful($project_id);

    /**
     * @param int $project_id
     *
     * @return int
     */
    public function getTodayCount($project_id);

    /**
     * @param int $project_id
     *
     * @return int
     */
    public function getLastWeekCount($project_id);

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
