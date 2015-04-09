<?php namespace App\Repositories;

use App\Deployment;

class DeploymentRepository
{
    public function getTimeline()
    {
        // Get the latest 15 deployments
        $raw_sql = 'project_id IN (SELECT id FROM projects WHERE deleted_at IS NULL)';
        return Deployment::whereRaw($raw_sql) // FIXME: Surely there is a nicer way to do this?
                         ->take(15)
                         ->orderBy('started_at', 'DESC')
                         ->get();
    }
}