<?php

namespace REBELinBLUE\Deployer\Repositories\Contracts;

interface DeploymentRepositoryInterface
{
    public function create(array $fields);
    public function getById($model_id);
    public function abort($model_id);
    public function rollback($model_id, array $optional = []);
    public function getLatest($project_id, $paginate = 15);
    public function getLatestSuccessful($project_id);
    public function getTimeline();
    public function getTodayCount($project_id);
    public function getLastWeekCount($project_id);
    public function getPending();
    public function getRunning();
}
