<?php

namespace App\Repositories\Contracts;

interface DeploymentRepositoryInterface
{
    public function create(array $fields);
    public function getById($model_id);
    public function getLatest($project_id, $paginate = 15);
    public function getTimeline();
    public function getTodayCount($project_id);
    public function getLastWeekCount($project_id);
}
