<?php

namespace App\Repositories;

use App\Deployment;
use App\Jobs\QueueDeployment;
use App\Repositories\Contracts\DeploymentRepositoryInterface;
use App\Repositories\EloquentRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * The deployment repository.
 */
class EloquentDeploymentRepository extends EloquentRepository implements DeploymentRepositoryInterface
{
    use DispatchesJobs;

    /**
     * Class constructor.
     *
     * @param  Deployment                   $model
     * @return EloquentDeploymentRepository
     */
    public function __construct(Deployment $model)
    {
        $this->model = $model;
    }

    /**
     * Creates a new instance of the server.
     *
     * @param  array $fields
     * @return Model
     */
    public function create(array $fields)
    {
        $optional = [];
        if (array_key_exists('optional', $fields)) {
            $optional = $fields['optional'];
            unset($fields['optional']);
        }

        $deployment = $this->model->create($fields);

        $this->dispatch(new QueueDeployment(
            $deployment->project,
            $deployment,
            $optional
        ));

        return $deployment;
    }

    /**
     * Gets the latest deployments for a project.
     *
     * @param  int   $project
     * @param  int   $paginate
     * @return array
     */
    public function getLatest($project_id, $paginate = 15)
    {
        return $this->model->where('project_id', $project_id)
                           ->with('user', 'project')
                           ->orderBy('started_at', 'DESC')
                           ->paginate($paginate);
    }

    /**
     * Gets the latest deployments for all projects.
     *
     * @return array
     */
    public function getTimeline()
    {
        $raw_sql = 'project_id IN (SELECT id FROM projects WHERE deleted_at IS NULL)';

        return $this->model->whereRaw($raw_sql)
                           ->with('project')
                           ->take(15)
                           ->orderBy('started_at', 'DESC')
                           ->get();
    }

    /**
     * Gets pending deployments.
     *
     * @return array
     */
    public function getPending()
    {
        return $this->getStatus(Deployment::PENDING);
    }

    /**
     * Gets running deployments.
     *
     * @return array
     */
    public function getRunning()
    {
        return $this->getStatus(Deployment::DEPLOYING);
    }

    /**
     * Gets the number of times a project has been deployed today.
     *
     * @param  int $project_id
     * @return int
     * @see DeploymentRepository::getBetweenDates()
     */
    public function getTodayCount($project_id)
    {
        $now = Carbon::now();

        return $this->getBetweenDates($project_id, $now, $now);
    }

    /**
     * Gets the number of times a project has been deployed in the last week.
     *
     * @param  int $project_id
     * @return int
     * @see DeploymentRepository::getBetweenDates()
     */
    public function getLastWeekCount($project_id)
    {
        $lastWeek  = Carbon::now()->subWeek();
        $yesterday = Carbon::now()->yesterday();

        return $this->getBetweenDates($project_id, $lastWeek, $yesterday);
    }

    /**
     * Gets the number of times a project has been deployed between the specified dates.
     *
     * @param  int    $project_id
     * @param  Carbon $startDate
     * @param  Carbon $endDate
     * @return int
     */
    private function getBetweenDates($project_id, Carbon $startDate, Carbon $endDate)
    {
        return $this->model->where('project_id', $project_id)
                           ->where('started_at', '>=', $startDate->format('Y-m-d') . ' 00:00:00')
                           ->where('started_at', '<=', $endDate->format('Y-m-d') . ' 23:59:59')
                           ->count();
    }

    /**
     * Gets deployments with a supplied status.
     *
     * @param  int   $status
     * @return array
     */
    private function getStatus($status)
    {
        $raw_sql = 'project_id IN (SELECT id FROM projects WHERE deleted_at IS NULL)';

        return $this->model->whereRaw($raw_sql)
                           ->where('status', $status)
                           ->orderBy('started_at', 'DESC')
                           ->get();
    }
}
