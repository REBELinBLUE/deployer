<?php

namespace REBELinBLUE\Deployer\Repositories;

use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Jobs\AbortDeployment;
use REBELinBLUE\Deployer\Jobs\QueueDeployment;
use REBELinBLUE\Deployer\Repositories\Contracts\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;

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

        // FIXME: Catch an error here and rollback model if it fails
        $this->dispatch(new QueueDeployment(
            $deployment->project,
            $deployment,
            $optional
        ));

        return $deployment;
    }

    /**
     * Sets a deployment to abort.
     *
     * @param  int  $model_id
     * @return void
     */
    public function abort($model_id)
    {
        $deployment = $this->getById($model_id);

        if (!$deployment->isAborting()) {
            $deployment->status = Deployment::ABORTING;
            $deployment->save();

            $this->dispatch(new AbortDeployment($deployment));
        }
    }

    /**
     * Creates a new deployment based on a previous one.
     *
     * @param  int  $model_id
     * @param  array $optional
     * @return void
     */
    public function rollback($model_id, array $optional = [])
    {
        $previous = $this->getById($model_id);

        return $this->create([
            'committer'       => $previous->committer,
            'committer_email' => $previous->committer_email,
            'commit'          => $previous->commit,
            'project_id'      => $previous->project_id,
            'branch'          => $previous->branch,
            'project_id'      => $previous->project_id,
            'optional'        => $optional,
        ]);
    }

    /**
     * Gets the latest deployments for a project.
     *
     * @param  int   $project_id
     * @param  int   $paginate
     * @return array
     */
    public function getLatest($project_id, $paginate = 15)
    {
        return $this->model->where('project_id', $project_id)
                           ->with('user', 'project')
                           ->whereNotNull('started_at')
                           ->orderBy('started_at', 'DESC')
                           ->paginate($paginate);
    }

    /**
     * Get the latest successful deployment for a project.
     *
     * @param  int   $project_id
     * @return array
     */
    public function getLatestSuccessful($project_id)
    {
        return $this->model->where('project_id', $project_id)
                           ->where('status', Deployment::COMPLETED)
                           ->whereNotNull('started_at')
                           ->orderBy('started_at', 'DESC')
                           ->first();
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
                           ->whereNotNull('started_at')
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
                           ->whereNotNull('started_at')
                           ->orderBy('started_at', 'DESC')
                           ->get();
    }
}
